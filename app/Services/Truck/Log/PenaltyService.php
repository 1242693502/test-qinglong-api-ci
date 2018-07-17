<?php

namespace App\Services\Truck\Log;

use App\Models\Driver\Driver;
use App\Models\Truck\Log\Penalty;
use App\Services\Truck\LogService;

/**
 * Class PenaltyService
 *
 * @package App\Services\Truck\Log
 */
class PenaltyService extends BaseService
{
    /**
     * 添加车辆罚款记录
     *
     * @param string $driverUUID
     * @param array  $attributes
     *
     * @return \App\Models\Truck\Log\Penalty
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function create($driverUUID, array $attributes)
    {
        $truckUUID = $this->getDrivingTruckUUID($driverUUID);
        $orderUUID = $this->getTruckCurrentOrderUUID($truckUUID);

        $attributes['order_uuid'] = $orderUUID;
        $penalty                  = Penalty::create(array_merge($attributes, [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
        ]));

        $driver = Driver::where('driver_uuid', $driverUUID)->first(['name']);

        $description = '车辆录入罚款信息，违章司机：' . ($driver->name) . ' 共扣分数：' . $attributes['penalty_points'] . '分 罚款： ' . ($attributes['total_price'] / 100) . ' 元' . ' 共包含照片：' . count($attributes['images']) . '张 违章日期：' . $attributes['penalty_date'];
        $images      = [];
        foreach ($attributes['images'] as $key => $code) {
            $images[] = [
                'name' => '罚款图片' . ($key + 1),
                'code' => $code,
            ];
        }
        // 日志写入总表
        (new LogService())->logPenalty($driverUUID, $truckUUID, $attributes, [
            'description' => $description,
            'images'      => $images,
        ]);

        return $penalty;
    }
}