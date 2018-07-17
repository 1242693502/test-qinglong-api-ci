<?php

namespace App\Services\Truck\Log;

use App\Models\Truck\Log\TollRoad;
use App\Services\Truck\LogService;

/**
 * Class TollRoadService
 *
 * @package App\Services\Truck\Log
 */
class TollRoadService extends BaseService
{
    /**
     * 添加车辆通行费记录
     *
     * @param string $driverUUID
     * @param array  $attributes
     *
     * @return \App\Models\Truck\Log\TollRoad
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function create($driverUUID, array $attributes)
    {
        $truckUUID = $this->getDrivingTruckUUID($driverUUID);
        $orderUUID = $this->getTruckCurrentOrderUUID($truckUUID);

        $attributes['order_uuid'] = $orderUUID;
        $tollRoad                 = TollRoad::create(array_merge($attributes, [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
        ]));

        $description = '车辆通行费：' . ($attributes['total_price'] / 100) . ' 元' . ' 共包含照片：' . count($attributes['images']) . '张';
        $images      = [];
        foreach ($attributes['images'] as $key => $code) {
            $images[] = [
                'name' => '车辆通行费照片' . ($key + 1),
                'code' => $code,
            ];
        }
        // 日志写入总表
        (new LogService())->logTollRoad($driverUUID, $truckUUID, $attributes, [
            'description' => $description,
            'images'      => $images,
        ]);

        return $tollRoad;
    }
}