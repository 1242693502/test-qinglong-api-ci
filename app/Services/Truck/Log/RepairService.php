<?php

namespace App\Services\Truck\Log;

use App\Models\Truck\Log\Repair;
use Illuminate\Support\Arr;
use Urland\Exceptions\Client;
use App\Services\Truck\LogService;

/**
 * Class RepairService
 *
 * @package App\Services\Truck\Log
 */
class RepairService extends BaseService
{
    /**
     * 添加维修保养记录
     *
     * @param string $driverUUID
     * @param array  $attributes
     *
     * @return \App\Models\Truck\Log\Repair
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function create($driverUUID, array $attributes)
    {
        // 检查维修类型是否存在
        $repairType   = Arr::pull($attributes, 'repair_type');
        $repairTypeId = cons('truck.log.repair_type.' . $repairType);
        if (is_null($repairTypeId)) {
            throw new Client\BadRequestException('维修类型不存在');
        }

        // 维修类型不等于其他的话，维修名称取对应的值
        $name = Arr::get($attributes, 'name');
        if ($repairType !== 'other') {
            $name = cons()->lang('truck.log.repair_type.' . $repairType);
        }

        // 判断是否存在维修名称
        if (empty($name)) {
            throw new Client\BadRequestException('请输入维修项目');
        }

        $truckUUID                    = $this->getDrivingTruckUUID($driverUUID);
        $orderUUID                    = $this->getTruckCurrentOrderUUID($truckUUID);
        $attributes['name']           = $name;
        $attributes['order_uuid']     = $orderUUID;
        $attributes['repair_type_id'] = $repairTypeId;
        $repair                       = Repair::create(array_merge($attributes, [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
        ]));

        $description = '维修项目：' . $repair->name . ' 维修费用：' . ($repair->total_price / 100) . ' 元' . ' 共包含照片：' . count($attributes['images']) . '张';
        $images      = [];
        foreach ($attributes['images'] as $key => $code) {
            $images[] = [
                'name' => '维修图片' . ($key + 1),
                'code' => $code,
            ];
        }

        // 日志写入总表
        (new LogService())->logRepair($driverUUID, $truckUUID, $attributes, [
            'description' => $description,
            'images'      => $images,
        ]);

        return $repair;
    }

}