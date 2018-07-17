<?php

namespace App\Services\Truck\Log;

use App\Services\Driver\DriverTruckService;
use \App\Services\BaseService as ParentService;
use App\Services\Order\MainLine\TruckService;
use Urland\Exceptions\Client;

class BaseService extends ParentService
{
    /**
     * 获取当前正在驾驶的车辆UUID
     *
     * @param string $driverUUID
     *
     * @return string
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    protected function getDrivingTruckUUID($driverUUID)
    {
        $drivingTruck = (new DriverTruckService())->getDrivingTruckByDriverUUID($driverUUID);
        if (empty($drivingTruck) || empty($drivingTruck->truck_uuid)) {
            throw new Client\BadRequestException('司机当前非正在驾驶状态,禁止操作');
        }

        return $drivingTruck->truck_uuid;
    }

    /**
     * 获取当前订单ID
     *
     * @param string $truckUUID
     *
     * @return null|string
     */
    protected function getTruckCurrentOrderUUID($truckUUID)
    {
        $currentOrder = (new TruckService())->getCurrentOrder($truckUUID);
        return $currentOrder ? $currentOrder->order_uuid : null;
    }
}