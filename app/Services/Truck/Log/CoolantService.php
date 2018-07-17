<?php

namespace App\Services\Truck\Log;

use App\Models\Truck\Log\Coolant;
use App\Services\Truck\LogService;

/**
 * Class CoolantService
 *
 * @package App\Services\Truck\Log
 */
class CoolantService extends BaseService
{
    /**
     * 添加加水记录
     *
     * @param string $driverUUID
     * @param array  $attributes
     *
     * @return \App\Models\Truck\Log\Coolant
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function create($driverUUID, array $attributes)
    {
        $truckUUID = $this->getDrivingTruckUUID($driverUUID);
        $orderUUID = $this->getTruckCurrentOrderUUID($truckUUID);

        $attributes['order_uuid'] = $orderUUID;
        $coolant                  = Coolant::create(array_merge($attributes, [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
        ]));

        $description = '车辆录入加水费用，加水：' . ($attributes['liter_number'] / 1000) . ' L' . ' 录入：' . ($attributes['total_price'] / 100) . ' 元' . ' 共包含照片：' . count($attributes['images']) . '张';
        $images      = [];
        foreach ($attributes['images'] as $key => $code) {
            $images[] = [
                'name' => '加水费用照片' . ($key + 1),
                'code' => $code,
            ];
        }
        // 日志写入总表
        (new LogService())->logCoolant($driverUUID, $truckUUID, $attributes, [
            'description' => $description,
            'images'      => $images,
        ]);

        return $coolant;
    }
}