<?php

namespace App\Services\Driver;

use App\Models\Driver\DriverTruckLog;
use App\Models\Truck\Truck;
use App\Services\Order\MainLine\LogService;
use Carbon\Carbon;
use App\Models\Driver\DriverTruck;
use App\Services\BaseService;
use App\Services\Order\MainLine\TruckService;
use Illuminate\Support\Arr;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

/**
 * Class DriverService
 *
 * @package App\Services\Driver
 */
class DriverTruckService extends BaseService
{
    /**
     * 获取司机正在驾驶的车辆（只获取正在驾驶的司机）
     *
     * @param string $driverUUID
     *
     * @return \App\Models\Driver\DriverTruck|null
     */
    public function getDrivingTruckByDriverUUID($driverUUID)
    {
        return DriverTruck::where('driver_uuid', $driverUUID)->where('is_driving', true)->first();
    }

    /**
     * 副司机切换到主司机（主副司机换班）
     *
     * @param string $truckUUID
     * @param string $driverUUID
     * @param array  $attributes 换班数据
     *
     * @return \App\Models\Driver\DriverTruck
     * @throws Server\InternalServerException
     */
    public function swapDriving($truckUUID, $driverUUID, $attributes = [])
    {
        //0. 检查司机是否正在关联当前车辆,检查司机是否为副司机
        //1. 到 driver_truck 表搜索 truck_uuid 的 主司机信息
        //2. 更改主司机 is_driving = 0 并记录 driver_truck_logs
        //3. 更改副司机 is_driving = 1 并记录 driver_truck_logs
        //返回主司机 driver_truck 结果

        $driverTrucks = DriverTruck::where('truck_uuid', $truckUUID)->get();

        if ($driverTrucks->isEmpty()) {
            throw new Server\InternalServerException('该车辆没有关联司机');
        }

        $driverUUIDToIsDrivings = $driverTrucks->pluck('is_driving', 'driver_uuid');
        if (!$driverUUIDToIsDrivings->offsetExists($driverUUID)) {
            throw new Server\InternalServerException('该司机没有关联该车辆');
        }

        if ($driverUUIDToIsDrivings[$driverUUID] != false) {
            throw new Server\InternalServerException('仅副司机允许提交换班');
        }

        //司机绑定车辆后第一次切换主副司机状态
        $originDriverTruck = $driverTrucks->where('is_driving', true)->first();
        if ($originDriverTruck) {
            if (!$originDriverTruck->setAttribute('is_driving', false)->save()) {
                throw new Server\InternalServerException('主司机切换副司机失败');
            }

            DriverTruckLog::create([
                'driver_uuid' => $originDriverTruck->driver_uuid,
                'is_driving'  => false,
                'truck_uuid'  => $originDriverTruck->truck_uuid,
                'mileage'     => 0,
                'remark'      => '主司机切换到副司机',
            ]);
        }

        $nowDriverTruck = $driverTrucks->where('driver_uuid', $driverUUID)->first();
        if (!$nowDriverTruck->setAttribute('is_driving', true)->save()) {
            throw new Server\InternalServerException('副司机切换主司机失败');
        }

        DriverTruckLog::create([
            'driver_uuid' => $nowDriverTruck->driver_uuid,
            'is_driving'  => true,
            'truck_uuid'  => $nowDriverTruck->truck_uuid,
            'mileage'     => 0,
            'remark'      => '副司机切换到主司机',
        ]);

        // 记录日志
        $originDriverInfo = "无";
        $originDriverUUID = '';
        if ($originDriverTruck) {
            $originDriverUUID = $originDriverTruck->driver_uuid;
            $originDriver     = $originDriverTruck->driver;
            $originDriverInfo = $originDriver->name . '（' . $originDriver->phone . '）';
        }
        $nowDriver     = $nowDriverTruck->driver;
        $nowDriverInfo = $nowDriver->name . '（' . $nowDriver->phone . '）';
        $hasExceptions = Arr::get($attributes, 'has_exceptions') ? '是' : '否';
        $description   = '主副司机换班，原主司机：' . $originDriverInfo . ' 现主司机：' . $nowDriverInfo . ' 共包含照片：' . count($attributes['images']) . ' 张 是否异常：' . $hasExceptions;

        $images = [];
        foreach ($attributes['images'] as $key => $code) {
            $images[] = [
                'name' => '换班异常照片' . ($key + 1),
                'code' => $code,
            ];
        }
        $attributes = array_merge($attributes, [
            'driver_uuid'        => $driverUUID,
            'origin_driver_uuid' => $originDriverUUID,
        ]);

        $processedData = [
            'description' => $description,
            'images'      => $images,
        ];

        // 记录订单日志
        $orderMainLine = (new TruckService())->getCurrentOrder($truckUUID);
        if ($orderMainLine) {
            $orderMainLineLog = (new LogService())->logSwapDriving($orderMainLine, $driverUUID, $attributes,
                $processedData);
            if (!$orderMainLineLog->exists) {
                throw new Server\InternalServerException('记录订单日志失败');
            }
        }

        return $nowDriverTruck;
    }

    /**
     * 司机绑定车辆
     *
     * @param string   $truckUUID
     * @param array    $driverUUIDs    副司机列表
     * @param null|int $mainDriverUUID 主司机UUID
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function appointDrivers($truckUUID, $driverUUIDs = [], $mainDriverUUID = null)
    {
        /*
         * 1. 车辆空闲验证
         *
         * 2. 司机空闲验证
         *
         * 3. 插入driver_truck_logs
         *
         * 4. 更新driver_truck
         */

        // 车辆空闲验证
        $trucks = Truck::where('truck_uuid', $truckUUID)->first();
        if (empty($trucks)) {
            throw new Client\NotFoundException('获取车辆信息失败');
        }
        if (!$trucks->is_available) {
            throw new Client\BadRequestException('当前车辆不可用');
        }

        // 司机空闲检测（是否已绑定其他车辆）
        $driverTruckList = [];
        foreach ($driverUUIDs as $driverUUID) {
            if ($driverUUID === $mainDriverUUID) {
                throw new Client\BadRequestException('主副司机不能为同一人');
            }

            $driverTruck = DriverTruck::where('driver_uuid', $driverUUID)->first();

            // 司机关联不存在
            if (!$driverTruck) {
                continue;
            }

            // 司机原来有绑定其他车辆
            if ($driverTruck->truck_uuid && $driverTruck->truck_uuid !== $truckUUID) {
                $trucks = $driverTruck->truck;
                if (!empty($trucks)/* && !$trucks->is_available*/) {
                    $drivers = $driverTruck->driver;
                    throw new Client\BadRequestException('司机' . $drivers->name . '（' . $drivers->phone . '）已关联车辆 ' . $trucks->license_plate_number . '，目前不可更换关联车辆。请联系后台相关人员处理！');
                }
            }

            $driverTruckList[] = $driverTruck;
        }

        $mainDriverTruck = DriverTruck::where('driver_uuid', $mainDriverUUID)->first();
        if (empty($mainDriverTruck)) {
            throw new Client\BadRequestException('获取主司机信息失败');
        }
        if ($mainDriverTruck->truck_uuid && $mainDriverTruck->truck_uuid !== $truckUUID) {
            $trucks = $mainDriverTruck->truck;
            if (!empty($trucks)/* && !$trucks->is_available*/) {
                $drivers = $mainDriverTruck->driver;
                throw new Client\BadRequestException('司机' . $drivers->name . '（' . $drivers->phone . '）已关联车辆 ' . $trucks->license_plate_number . '，目前不可更换关联车辆。请联系后台相关人员处理！');
            }
        }

        // 清空原有关联
        $this->removeDriversByTruckUUID($truckUUID);

        foreach ($driverTruckList as $item) {
            // 解除当前司机原有车辆的所有绑定关系
            /*if ($driverTruck->truck_uuid && $driverTruck->truck_uuid !== $truckUUID) {
                $this->removeDriversByTruckUUID($driverTruck->truck_uuid, '司机绑定其他车辆，自动解绑当前车辆的所有绑定关系');
            }*/

            // 这里的 $driverTruck 可能会被其他方法改变，这里要重新获取一次
            $driverTruck = $item->fresh();
            // 记录日志
            DriverTruckLog::create([
                'driver_uuid' => $driverTruck->driver_uuid,
                'is_driving'  => false,
                'truck_uuid'  => $truckUUID,
                'mileage'     => 0,
                'remark'      => '司机绑定车辆',
            ]);

            $driverTruck->fill(['truck_uuid' => $truckUUID, 'is_driving' => false])->save();
        }

        // 插入主司机
        DriverTruckLog::create([
            'driver_uuid' => $mainDriverUUID,
            'is_driving'  => true,
            'truck_uuid'  => $truckUUID,
            'mileage'     => 0,
            'remark'      => '主司机绑定车辆',
        ]);
        // 这里的 $mainDriverTruck 可能会被其他方法改变，所以这里要重新获取一次
        $mainDriverTruck = $mainDriverTruck->fresh();
        $mainDriverTruck->fill(['truck_uuid' => $truckUUID, 'is_driving' => true])->save();
    }

    /**
     * 司机解绑
     *
     * @param string $truckUUID
     * @param string $remark
     */
    public function removeDriversByTruckUUID($truckUUID, $remark = '')
    {
        /*
         * 1. 通过driver_truck获取所有带有driver_uuid的记录
         * 2. 将以上记录循环清空truck_uuid，并插入到driver_truck_logs
         * 3. 此方法不需要返回
         */

        $driverTrucks = DriverTruck::where('truck_uuid', $truckUUID)->whereNotNUll('driver_uuid')->get();
        foreach ($driverTrucks as $driverTruck) {
            //TODO
            // 完善mileage

            // 插入driver_truck_logs
            DriverTruckLog::create([
                'driver_uuid' => $driverTruck->driver_uuid,
                'is_driving'  => false,
                'truck_uuid'  => $truckUUID,
                'mileage'     => 0,
                'remark'      => $remark,
            ]);

            // truck_uuid设为null，is_driving设为false
            $driverTruck->fill(['truck_uuid' => null, 'is_driving' => false])->save();
        }
    }

}