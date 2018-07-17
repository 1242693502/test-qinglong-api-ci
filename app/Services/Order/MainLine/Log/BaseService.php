<?php

namespace App\Services\Order\MainLine\Log;

use App\Models\Driver\DriverTruck;
use App\Models\Order\MainLine\Place;
use App\Models\Order\OrderMainLine;
use App\Services\Order\MainLine\ActionService;
use Urland\Exceptions\Client;
use App\Services\BaseService as ParentService;

class BaseService extends ParentService
{
    /**
     * 获取司机驾驶中的车辆UUID
     *
     * @param $driverUUID
     *
     * @return mixed
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    protected function getDrivingTruckByDriverUUID($driverUUID)
    {
        // 检查司机是否正在驾驶某车辆（需要判断is_driving是否为true）
        $driverTruck = DriverTruck::where('driver_uuid', $driverUUID)->where('is_driving', true)->first();
        if (!$driverTruck) {
            throw new Client\BadRequestException('该司机无驾驶中的车辆');
        }
        return $driverTruck->truck_uuid;
    }

    /**
     * 根据订单UUID、车辆UUID获取订单记录
     *
     * @param $orderUUID
     * @param $truckUUID
     *
     * @return \App\Models\Order\OrderMainLine
     * @throws \Urland\Exceptions\Client\ForbiddenException
     */
    protected function getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID)
    {
        // 检查正在驾驶的车辆是否能操作该订单
        $orderMainLine = OrderMainLine::where('order_uuid', $orderUUID)->where('truck_uuid', $truckUUID)->first();
        if (!$orderMainLine) {
            throw new Client\ForbiddenException('该车辆不能操作该订单');
        }
        if (!$orderMainLine->orderTruck || !$orderMainLine->orderTruck->is_available) {
            throw new Client\ForbiddenException('车辆状态不可用');
        }
        return $orderMainLine;
    }

    /**
     * 判断是否允许操作
     *
     * @param $orderUUID
     * @param $typeKey
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    protected function checkActionAllow($orderUUID, $typeKey)
    {
        $stage = ActionService::serviceForOrderUUID($orderUUID)->stage();
        if (!$stage || !$stage->action($typeKey) || !$stage->action($typeKey)->computedAllow()) {
            throw new Client\ForbiddenException('禁止操作');
        }
    }

    /**
     * 根据订单uuid获取当前装货地地址
     *
     * @param string $orderUUID
     *
     * @return \App\Models\Order\MainLine\Place
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    protected function getCurrentLoadingPlaceByOrderUUID($orderUUID)
    {
        return $this->getCurrentPlaceByOrderUUID($orderUUID, cons('order.mainline.place.type.loading'));
    }

    /**
     * 根据订单uuid获取当前卸货地地址
     *
     * @param string $orderUUID
     *
     * @return \App\Models\Order\MainLine\Place
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    protected function getCurrentUnloadingPlaceByOrderUUID($orderUUID)
    {
        return $this->getCurrentPlaceByOrderUUID($orderUUID, cons('order.mainline.place.type.unloading'));
    }

    /**
     * 根据订单uuid和装卸货地址类型获取当前装卸货地
     *
     * @param string $orderUUID
     * @param int    $placeType
     *
     * @return \App\Models\Order\MainLine\Place
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    private function getCurrentPlaceByOrderUUID($orderUUID, $placeType)
    {
        $orderMainLinePlace = Place::where('order_uuid', $orderUUID)->where('type',
            $placeType)->orderByDesc('arrival_time')->first();
        if (empty($orderMainLinePlace) || !$orderMainLinePlace->arrival_time || $orderMainLinePlace->departure_time) {
            throw new Client\BadRequestException('该司机尚未到达指定地点');
        }

        return $orderMainLinePlace;
    }
}