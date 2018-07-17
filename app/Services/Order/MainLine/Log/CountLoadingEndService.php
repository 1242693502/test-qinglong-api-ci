<?php

namespace App\Services\Order\MainLine\Log;

use App\Services\Order\MainLine\LogService;
use Carbon\Carbon;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

class CountLoadingEndService extends BaseService
{
    /**
     * 记录装货计时结束
     *
     * @param       $orderUUID
     * @param       $driverUUID
     * @param array $parameters
     *
     * @return \App\Models\Order\MainLine\Log
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function create($orderUUID, $driverUUID, $parameters = [])
    {
        /*
         * 1. 检查司机是否正在驾驶某车辆（需要判断is_driving是否为true）
         * 2. 检查正在驾驶的车辆是否能操作该订单
         * 3. 调用LogService，记录$parameters到日志
         * 4. 返回记录的日志
         */

        // 检查是否允许操作
        $this->checkActionAllow($orderUUID, 'count_loading_end');

        // 检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        // 检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        // 获取当前装货地地址
        $mainLinePlace = $this->getCurrentLoadingPlaceByOrderUUID($orderUUID);

        // 完成计时
        if ($mainLinePlace->count_end_time) {
            throw new Client\BadRequestException('已完成计时');
        }

        // 记录装货结束计时时间
        $mainLinePlace->setAttribute('count_end_time', Carbon::now())->save();

        $data = [
            'order_uuid'   => $orderUUID,
            'driver_uuid'  => $driverUUID,
            'truck_uuid'   => $truckUUID,
            'place_uuid'   => $mainLinePlace->place_uuid,
            'full_address' => $mainLinePlace->area_name . $mainLinePlace->address,
        ];
        //记录$parameters
        $description      = '装货计时结束，装货地：' . $data['full_address'];
        $orderMainLineLog = (new LogService())->logCountLoadingEnd($orderMainLine, $driverUUID,
            array_merge($data, $parameters), [
                'description' => $description,
                'images'      => [],
            ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }
        return $orderMainLineLog;
    }
}