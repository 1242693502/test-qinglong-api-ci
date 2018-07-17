<?php

namespace App\Services\Truck\Log;

use App\Models\Truck\Log\AdBlue;
use App\Services\Truck\LogService;

/**
 * Class AdBlueService
 *
 * @package App\Services\Truck\Log
 */
class AdBlueService extends BaseService
{
    /**
     * 添加尿素记录
     *
     * @param string $driverUUID
     * @param array  $attributes
     *
     * @return \App\Models\Truck\Log\AdBlue
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function create($driverUUID, array $attributes)
    {
        $truckUUID = $this->getDrivingTruckUUID($driverUUID);
        $orderUUID = $this->getTruckCurrentOrderUUID($truckUUID);

        $attributes['order_uuid'] = $orderUUID;
        $adBlue                   = AdBlue::create(array_merge($attributes, [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
        ]));

        $description = '车辆领用尿素，领用：' . ($attributes['liter_number'] / 1000) . ' L' . ' 共包含照片：' . count($attributes['images']) . '张';
        $images      = [];
        foreach ($attributes['images'] as $key => $code) {
            $images[] = [
                'name' => '领用尿素照片' . ($key + 1),
                'code' => $code,
            ];
        }
        // 日志写入总表
        (new LogService())->logAdblue($driverUUID, $truckUUID, $attributes, [
            'description' => $description,
            'images'      => $images,
        ]);

        return $adBlue;
    }
}