<?php

namespace App\Services\Truck\Log;

use App\Models\Truck\Log\Park;
use App\Services\Truck\LogService;

/**
 * Class ParkService
 *
 * @package App\Services\Truck\Log
 */
class ParkService extends BaseService
{

    /**
     * 添加停车记录
     *
     * @param string $driverUUID
     * @param array  $attributes
     *
     * @return \App\Models\Truck\Log\Park
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function create($driverUUID, array $attributes)
    {
        $truckUUID = $this->getDrivingTruckUUID($driverUUID);
        $orderUUID = $this->getTruckCurrentOrderUUID($truckUUID);

        $attributes['order_uuid'] = $orderUUID;
        $park                     = Park::create(array_merge($attributes, [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
        ]));

        $description = '车辆录入停车费用，录入：' . ($attributes['total_price'] / 100) . ' 元' . ' 共包含照片：' . count($attributes['images']) . '张';
        $images      = [];
        foreach ($attributes['images'] as $key => $code) {
            $images[] = [
                'name' => '录入停车费用照片' . ($key + 1),
                'code' => $code,
            ];
        }
        // 日志写入总表
        (new LogService())->logPark($driverUUID, $truckUUID, $attributes, [
            'description' => $description,
            'images'      => $images,
        ]);

        return $park;

    }

}