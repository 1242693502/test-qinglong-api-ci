<?php

namespace App\Services\Truck\Log;

use App\Models\GasCard\FillingStation;
use App\Models\Truck\Log\Refuel;
use App\Models\Truck\TruckGasCard;
use App\Services\Truck\LogService;
use Illuminate\Support\Arr;
use Urland\Exceptions\Client;

/**
 * Class RefuelService
 *
 * @package App\Services\Truck\Log
 */
class RefuelService extends BaseService
{
    /**
     * 添加成本-加油记录
     *
     * @param string $driverUUID
     * @param array  $attributes
     *
     * @return \App\Models\Truck\Log\Refuel|\Illuminate\Database\Eloquent\Model
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public function create($driverUUID, array $attributes)
    {
        // 检查付款类型是否存在
        $refuelPayType   = Arr::pull($attributes, 'pay_type');
        $refuelPayTypeId = cons('truck.log.refuel_pay_type.' . $refuelPayType);
        if (is_null($refuelPayTypeId)) {
            throw new Client\BadRequestException('付款类型不存在');
        }
        $attributes['pay_type_id'] = $refuelPayTypeId;

        $truckUUID = $this->getDrivingTruckUUID($driverUUID);

        $fillingStation = null;
        switch ($refuelPayType) {
            case 'fixed':
                $attributes['gas_card_no'] = null;
                // 检查加油站是否存在
                $stationId      = Arr::get($attributes, 'filling_station_id');
                $fillingStation = FillingStation::where('id', $stationId)->first(['name']);
                if (!$fillingStation) {
                    throw new Client\NotFoundException('加油站不存在');
                }
                break;

            case 'gas_card':
                $attributes['filling_station_id'] = null;
                // 检查油卡是否存在
                $gasCardNo    = Arr::get($attributes, 'gas_card_no');
                $truckGasCard = TruckGasCard::where('gas_card_no', $gasCardNo)->first();
                if (!$truckGasCard || $truckGasCard->truck_uuid !== $truckUUID) {
                    throw new Client\NotFoundException('油卡不存在');
                }

                // 检查油卡状态是否正常
                if ($truckGasCard->status !== cons('truck.gas_card.status.normal')) {
                    throw new Client\ForbiddenException('当前油卡禁止进行加油');
                }
                break;

            case 'cash':
                // 非油卡加油，清空油站和加油卡
                $attributes['filling_station_id'] = null;
                $attributes['gas_card_no']        = null;
                break;
        }


        // 如果客户端不传总价，则自动计算
        if (is_null(Arr::get($attributes, 'total_price'))) {
            $perPrice    = (int)Arr::get($attributes, 'per_price');
            $literNumber = (int)Arr::get($attributes, 'liter_number');
            $totalPrice  = (int)ceil($perPrice * $literNumber / 1000);

            $attributes['total_price'] = $totalPrice;
        }

        $attributes['order_uuid'] = $this->getTruckCurrentOrderUUID($truckUUID);
        $refuel                   = Refuel::create(array_merge($attributes, [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
        ]));

        $description = '';
        switch ($refuelPayType) {
            case 'fixed':
                // 定点加油
                $description = '加油站名称：' . $fillingStation->name . ' 定点加油：' . ($refuel->liter_number) / 1000 . 'L  ' . ($refuel->total_price) / 100 . '元';
                break;
            case 'gas_card':
                // 油卡加油
                $description = '卡号：' . $refuel->gas_card_no . ' 油卡加油：' . ($refuel->liter_number) / 1000 . 'L  ' . ($refuel->total_price) / 100 . '元';
                break;
            case 'cash':
                // 现金加油
                $description = '现金加油：' . ($refuel->liter_number) / 1000 . 'L  ' . ($refuel->total_price) / 100 . '元';
                break;
        }

        $description .= ' 共包含照片：' . count($attributes['images']) . '张';

        $images = [];
        foreach ($attributes['images'] as $key => $code) {
            $images[] = [
                'name' => '录入加油费用照片' . ($key + 1),
                'code' => $code,
            ];
        }
        // 日志写入总表
        (new LogService())->logRefuel($driverUUID, $truckUUID, $attributes, [
            'description_append_mileage' => true,
            'description'                => $description,
            'images'                     => $images,
        ]);

        return $refuel;
    }

}