<?php

namespace App\Services\Truck;

use App\Models\Truck\Truck;
use App\Models\Truck\TruckGasCard;
use App\Services\BaseService;
use Carbon\Carbon;
use QingLong\Platform\GasCard\GasCard;
use Urland\Exceptions\Client\ForbiddenException;
use Urland\Exceptions\Client\NotFoundException;

class TruckGasCardService extends BaseService
{
    /**
     * 车辆关联油卡
     *
     * @param string $truckUUID
     * @param string $gasCardNo
     *
     * @return mixed
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function bindGasCard($truckUUID, $gasCardNo)
    {
        $gasCard = new GasCard();
        $gasCard->setClient('GasCard/GasCard');
        $bindInfo = $gasCard->execute('getInfoByCardNo', [$gasCardNo, ['t_front_plate']]);
        if (!empty($bindInfo['t_front_plate'])) {
            throw new ForbiddenException('该油卡已绑定其他车辆');
        }
        $trucks = Truck::where('truck_uuid', $truckUUID)->first(['id', 'license_plate_number']);
        if (empty($trucks)) {
            throw new NotFoundException('获取车辆信息失败');
        }

        $applyData   = [
            't_sid'         => $trucks->id,
            't_front_plate' => $trucks->license_plate_number,
        ];
        $gasCardInfo = $gasCard->execute('applyGasCard', [$gasCardNo, $applyData]);

        $truckGasCards = TruckGasCard::create([
            'truck_uuid'  => $truckUUID,
            'gas_card_no' => $gasCardNo,
            'bind_time'   => Carbon::now(),
            'channel'     => cons()->valueLang('truck.gas_card.channel', $gasCardInfo['gc_com_code']),
            'status'      => cons('truck.gas_card.status.normal'),
        ]);

        return $truckGasCards;
    }

    /**
     * 油卡挂失处理
     *
     * @param string $gasCardNo
     * @param string $reason
     *
     * @return \App\Models\Truck\TruckGasCard
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     */
    public function loss($gasCardNo, $reason)
    {
        $statusList    = cons('truck.gas_card.status');
        $truckGasCards = TruckGasCard::where('gas_card_no', $gasCardNo)->orderBy('id', 'desc')->firstOrFail();
        // 只有正常状态下的油卡才能挂失
        if (!in_array($truckGasCards->status, [$statusList['normal']])) {
            throw new ForbiddenException('只有正常状态下的油卡才能挂失');
        }

        $fillData = [
            'status'      => $statusList['lose'],
            'loss_time'   => Carbon::now(),
            'loss_reason' => $reason,
        ];
        $truckGasCards->fill($fillData)->save();
        return $truckGasCards;
    }

    /**
     * 解绑油卡
     *
     * @param string $gasCardNo
     * @param string $reason
     *
     * @return \App\Models\Truck\TruckGasCard
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function unbind($gasCardNo, $reason)
    {
        $statusList = cons('truck.gas_card.status');
        // 只有正常状态和挂失状态下的油卡才能解绑，解绑后的油卡可能会绑定给其他车辆
        $truckGasCards = TruckGasCard::where('gas_card_no', $gasCardNo)->whereIn('status',
            [$statusList['normal'], $statusList['lose']])->firstOrFail();

        $fillData = [
            'status'        => $statusList['cancel'],
            'unbind_time'   => Carbon::now(),
            'unbind_reason' => $reason,
        ];
        $truckGasCards->fill($fillData)->save();
        // 通知油卡系统释放油卡
        $gasCard = new GasCard();
        $gasCard->setClient('GasCard/GasCard');
        $gasCard->execute('setGasCardFree', [$gasCardNo]);
        return $truckGasCards;
    }
}