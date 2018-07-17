<?php

namespace App\Services\Order\MainLine\Log;

use App\Models\Truck;
use App\Services\Order\MainLine\LogService;
use Urland\Exceptions\Server;

class RecordWeightService extends BaseService
{
    /**
     * 录过磅单
     *
     * @param       $orderUUID
     * @param       $driverUUID
     * @param array $parameters
     *
     * @return \App\Models\Order\MainLine\Log
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function create($orderUUID, $driverUUID, array $parameters = [])
    {
        /*
         * 1. order_mainline_log 更新过磅重量
         * 2. order_mainline_logs 插入订单过磅日志
         * 3. truck_other_logs 插入车辆其他日志（过磅费用）
         * 4. truck_logs 插入车辆日志
         */

        // 检查是否允许操作
        $this->checkActionAllow($orderUUID, 'record_weight');

        // 检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        // 检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        // 获取当前装货地地址
        $mainLinePlace = $this->getCurrentLoadingPlaceByOrderUUID($orderUUID);

        // 记录过磅重量
        $orderMainLineUpdate = $orderMainLine->fill(['goods_weight' => $parameters['goods_weight']])->save();
        if (!$orderMainLineUpdate) {
            throw new Server\InternalServerException('更新过磅重量失败');
        }

        // 记录parameters
        $data        = [
            'place_uuid'   => $mainLinePlace->place_uuid,
            'full_address' => $mainLinePlace->area_name . $mainLinePlace->address,
        ];
        $description = '录过磅单: ' . $data['full_address'];
        $images      = [];
        foreach ($parameters['images'] as $key => $code) {
            $images[] = [
                'name' => '过磅单照片' . ($key + 1),
                'code' => $code,
            ];
        }
        $orderMainLineLog = (new LogService())->logRecordWeight($orderMainLine, $driverUUID,
            array_merge($data, $parameters), [
                'description' => $description,
                'images'      => $images,
            ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }

        // 记录truck_other_logs
        $truckOtherLogAttributes = [
            'truck_uuid'    => $truckUUID,
            'driver_uuid'   => $driverUUID,
            'order_uuid'    => $orderUUID,
            'name'          => '录过磅单',
            'total_price'   => $parameters['total_price'],
            'images'        => $parameters['images'],
            'has_invoice'   => $parameters['has_invoice'] ?? false,
            'merchant_name' => $parameters['merchant_name'] ?? null,
            'longitude'     => $parameters['longitude'] ?? null,
            'latitude'      => $parameters['latitude'] ?? null,
            'remark'        => $parameters['remark'] ?? null,
        ];
        $truckOtherLog           = Truck\Log\Other::create($truckOtherLogAttributes);

        if (!$truckOtherLog->exists) {
            throw new Server\InternalServerException('记录车辆费用日志失败');
        }

        // 记录truck_logs
        $truckLogAttributes = [
            'truck_uuid'      => $truckUUID,
            'driver_uuid'     => $driverUUID,
            'order_uuid'      => $orderUUID,
            'type'            => cons('truck.log.type.weight'),
            'title'           => cons()->lang('truck.log.type.weight'),
            'description'     => $description,
            'images'          => $parameters['images'],
            'status'          => 1,
            'remark'          => $parameters['remark'] ?? null,
            'current_mileage' => $parameters['current_mileage'] ?? null,
            'longitude'       => $truckOtherLog->longitude,
            'latitude'        => $truckOtherLog->latitude,
        ];

        $truckLog = Truck\TruckLog::create($truckLogAttributes);
        if (!$truckLog->exists) {
            throw new Server\InternalServerException('记录车辆日志失败');
        }
        return $orderMainLineLog;
    }

}