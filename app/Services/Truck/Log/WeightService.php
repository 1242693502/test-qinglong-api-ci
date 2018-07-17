<?php

namespace App\Services\Truck\Log;

use App\Models\Order\MainLine\Place;
use App\Models\Order\OrderMainLine;
use App\Models\Truck\Log\Other;
use App\Services\Order\MainLine;
use App\Services\Truck;
use Illuminate\Support\Arr;
use Urland\Exceptions\Server;

class WeightService extends BaseService
{
    /**
     * 录过磅单（随时可录入）
     *
     * @param $driverUUID
     * @param $attributes
     *
     * @return \App\Models\Truck\Log\Other
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function create($driverUUID, $attributes)
    {
        $truckUUID   = $this->getDrivingTruckUUID($driverUUID);
        $orderUUID   = $this->getTruckCurrentOrderUUID($truckUUID);

        $description = '录过磅单，过磅重量：' . $attributes['goods_weight'] / 1000 . ' 吨 费用总价：' . $attributes['total_price'] / 100 . ' 元' . ' 共包含照片：' . count($attributes['images']) . '张';
        $images      = [];
        foreach ($attributes['images'] as $key => $code) {
            $images[] = [
                'name' => '过磅单照片' . ($key + 1),
                'code' => $code,
            ];
        }

        if ($orderUUID) {
            // 订单及装\卸货地址
            $orderMainLine             = OrderMainLine::where('order_uuid', $orderUUID)->firstOrFail();
            $orderMainLinePlaceAddress = $this->getCurrentOrderMainLinePlace($orderMainLine);

            // 补充描述信息
            $description .= $orderMainLinePlaceAddress ? ' 地址：' . $orderMainLinePlaceAddress['full_address'] : '';

            // 更新订单货物重量
            $orderMainLine->setAttribute('goods_weight', Arr::get($attributes, 'goods_weight'))->save();

            // 记录订单日志 order_mainline_logs
            (new MainLine\LogService())->logRecordWeight($orderMainLine, $driverUUID,
                array_merge($orderMainLinePlaceAddress, $attributes), [
                    'description' => $description,
                    'images'      => $images,
                ]);
        }

        // 记录车辆其他日志 truck_other_logs
        $truckOtherLog = Other::create(array_merge($attributes, [
            'name'        => '录过磅单',
            'order_uuid'  => $orderUUID,
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
        ]));
        if (!$truckOtherLog->exists) {
            throw new Server\InternalServerException('记录车辆费用日志失败');
        }

        // 记录车辆日志 truck_logs
        $attributes['order_uuid'] = $orderUUID;
        (new Truck\LogService())->logWeight($driverUUID, $truckUUID, $attributes, [
            'description' => $description,
            'images'      => $images,
        ]);

        return $truckOtherLog;
    }

    /**
     * 根据订单当前状态，获取装（卸）货地址
     *
     * @param \App\Models\Order\OrderMainLine $orderMainLine
     *
     * @return array|null
     */
    private function getCurrentOrderMainLinePlace(OrderMainLine $orderMainLine)
    {
        $placeMapping = [
            cons('order.mainline.status.arrive_loading')   => cons('order.mainline.place.type.loading'),
            cons('order.mainline.status.arrive_unloading') => cons('order.mainline.place.type.unloading'),
        ];

        $orderMainLinePlaceAddress = [];
        if ($orderMainLine) {
            $placeType = Arr::get($placeMapping, $orderMainLine->order_status);
            if ($placeType) {
                $orderMainLinePlace = Place::where('order_uuid', $orderMainLine->order_uuid)->where('type',
                    $placeType)->orderByDesc('arrival_time')->first();
                if ($orderMainLinePlace && $orderMainLinePlace->arrival_time && !$orderMainLinePlace->departure_time) {
                    // 当前地址拼凑
                    $orderMainLinePlaceAddress = [
                        'place_uuid'   => $orderMainLinePlace->place_uuid,
                        'full_address' => $orderMainLinePlace->area_name . $orderMainLinePlace->address,
                    ];
                }
            }
        }
        return $orderMainLinePlaceAddress;
    }

}