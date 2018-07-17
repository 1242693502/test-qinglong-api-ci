<?php

namespace App\Services\Order\MainLine;

use App\Models\Driver\DriverTruck;
use App\Models\Order\MainLine;
use App\Models\Order\OrderMainLine;
use App\Services\BaseService;
use App\Services\Truck\TruckStatusService;
use Carbon\Carbon;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

/**
 * Class DriverService
 *
 * @package App\Services\Driver
 */
class DriverService extends BaseService
{
    /**
     * 司机确认接单操作
     *
     * @param $orderUUID
     * @param $driverUUID
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function driverConfirm($orderUUID, $driverUUID)
    {
        //1. 检查订单是否相关信息
        //1.1  订单是否已经指定了车辆
        //1.2  订单是否为 driver_confirm 状态
        //1.3  司机是否正在驾驶当前订单指定的车辆
        //2. 业务写逻辑
        //2.1 记录 order_mainline_logs 日志
        //2.2 将当前车辆的驾驶员记录到 order_mainline_driver 表 请看 type 常量
        //2.3 切换订单状态

        //检查是否允许操作
        $stage = ActionService::serviceForOrderUUID($orderUUID)->stage();
        if (!$stage || !$stage->action('driver_confirm') || !$stage->action('driver_confirm')->computedAllow()) {
            throw new Client\ForbiddenException('禁止操作');
        }

        //检查订单
        $orderMainLine = OrderMainLine::where('order_uuid', $orderUUID)->first();
        // 订单不存在
        if (!$orderMainLine) {
            throw Client\ValidationException::withMessages(['order_uuid' => '订单不存在']);
        }

        // 订单未指派车辆
        if (!$orderMainLine->truck_uuid) {
            throw new Client\BadRequestException('订单未指派车辆');
        }

        //司机车辆 验证
        $driverTruck = DriverTruck::where('driver_uuid', $driverUUID)->where('truck_uuid',
            $orderMainLine->truck_uuid)->first();

        // 司机未绑定订单车辆
        if (!$driverTruck) {
            throw new Client\ForbiddenException('接单失败，司机未绑定当前订单指定车辆');
        }

        // 司机是否为正在驾驶状态（驾驶中的司机才能接单）
        if (!$driverTruck->is_driving) {
            throw new Client\ForbiddenException('接单失败，司机未驾驶当前订单指定车辆');
        }

        // 车辆验证
        $truck = $orderMainLine->orderTruck;
        if (!$truck) {
            throw new Client\BadRequestException('订单指定车辆不存在');
        }

        // 验证订单当前状态（driver_confirm）是否允许切换 到 driver_prepare
        if (!((new StatusService())->canChangeStatus($orderMainLine->order_status, 'driver_prepare'))) {
            throw new Client\ForbiddenException('当前订单状态不允许接单');
        }
        // 验证车辆当前状态是否允许切换 到 in_transit
        if (!((new TruckStatusService())->canChangeStatus($truck, 'in_transit'))) {
            throw new Client\ForbiddenException('当前车辆状态不允许接单');
        }

        // 记录订单日志 order_mainline_logs
        $contentsParams   = [
            'driver_uuid'  => $driverUUID,
            'truck_uuid'   => $orderMainLine->truck_uuid,
            'driver_name'  => $driverTruck->driver->name,
            'driver_phone' => $driverTruck->driver->phone,
        ];
        $orderMainLineLog = (new LogService())->logDriverConfirm($orderMainLine, $driverUUID, $contentsParams, [
            'description' => '司机确认接单，接单司机：' . $driverTruck->driver->name . '（' . $driverTruck->driver->phone . '）',
            'images'      => [],
        ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }

        //记录接单信息
        // 记录order_mainline_driver
        $orderMainLineDriverData = [
            'order_uuid'   => $orderUUID,
            'driver_uuid'  => $driverUUID,
            'driver_name'  => $driverTruck->driver->name,
            'driver_phone' => $driverTruck->driver->phone,
            'type'         => cons('order.mainline.driver.type.confirm'),
            'status'       => cons('order.mainline.driver.status.normal'),
            'confirm_time' => Carbon::now(),
        ];
        $orderMainLineDriver     = MainLine\Driver::create($orderMainLineDriverData);

        // 将所有副司机添加到order_mainline_driver表中
        $driverTrucks = DriverTruck::with('driver')->where('truck_uuid',
            $orderMainLine->truck_uuid)->where('is_driving', false)->get();
        foreach ($driverTrucks as $driverTruck) {
            $orderMainLineDriverData = [
                'order_uuid'   => $orderUUID,
                'driver_uuid'  => $driverTruck->driver_uuid,
                'driver_name'  => $driverTruck->driver->name,
                'driver_phone' => $driverTruck->driver->phone,
                'type'         => cons('order.mainline.driver.type.follow'),
                'status'       => cons('order.mainline.driver.status.normal'),
                'confirm_time' => Carbon::now(),
            ];
            MainLine\Driver::create($orderMainLineDriverData);
        }

        if (!$orderMainLineDriver->exists) {
            throw new Server\InternalServerException('接单失败');
        }

        // 更新订单的接单时间
        $orderMainLine->setAttribute('confirm_time', Carbon::now())->save();

        // 订单状态切换到 driver_prepare
        (new StatusService())->changeStatus($orderMainLine, 'driver_prepare');

        //车辆状态切换到 in_transit
        (new TruckStatusService())->changeStatus($truck, 'in_transit');

    }


}