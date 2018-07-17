<?php

namespace App\Services\GasCard;

use App\Models\GasCard\GasCardOrder;
use App\Models\Truck\TruckGasCard;
use App\Services\BaseService;
use Carbon\Carbon;
use QingLong\Platform\GasCard\GasCard;
use Urland\Exceptions\Client\BadRequestException;
use Urland\Exceptions\Client\ForbiddenException;

class GasCardOrderService extends BaseService
{
    /**
     * 申请油卡充值
     *
     * @param string $gasCardNo
     * @param string $totalPrice
     *
     * @return mixed
     */
    public function applyRecharge($gasCardNo, $totalPrice)
    {
        $truckUUID = '';
        // 看油卡有无绑定车辆
        $truckGasCards = TruckGasCard::where('gas_card_no', $gasCardNo)->orderBy('id', 'desc')->first();
        if ($truckGasCards) {
            // 如果油卡状态不为解绑，则属于当前车辆
            if ($truckGasCards->status != cons('truck.gas_card.status.cancel')) {
                $truckUUID = $truckGasCards->truck_uuid;
            }
        }

        $orderData     = [
            'gas_card_no' => $gasCardNo,
            'total_price' => $totalPrice,
            'status'      => cons('truck.gas_card.order.waiting_appoval'),
            'truck_uuid'  => $truckUUID,
        ];
        $gasCardOrders = GasCardOrder::create($orderData);
        return $gasCardOrders;
    }

    /**
     * 审核充值油卡申请
     *
     * @param string $gasCardOrderUUID
     * @param array  $approvalData
     *
     * @return mixed
     * @throws BadRequestException
     * @throws ForbiddenException
     */
    public function approval($gasCardOrderUUID, $approvalData = [])
    {
        $statusList = cons('truck.gas_card.order');

        if (!in_array($approvalData['status'], [$statusList['cancel'], $statusList['waiting_recharge']])) {
            throw new BadRequestException('审核状态错误');
        }

        if ($approvalData['status'] == $statusList['cancel'] && empty($approvalData['approver_reason'])) {
            throw new BadRequestException('审核不通过时必须填写原因');
        }

        $gasCardOrders = GasCardOrder::where('gas_card_order_uuid', $gasCardOrderUUID)->firstOrFail();
        if ($gasCardOrders->status != $statusList['waiting_appoval']) {
            throw new ForbiddenException('油卡充值订单状态错误');
        }

        $approvalData['approver_time'] = Carbon::now();
        $gasCardOrders->fill($approvalData)->save();

        // 通知油卡系统充值
        if ($approvalData['status'] == $statusList['waiting_recharge']) {
            $gasCard = new GasCard();
            $gasCard->setClient('GasCard/GasCardOrder');
            $gasCard->execute('applyRecharge', [
                $gasCardOrders['gas_card_order_uuid'],
                $gasCardOrders['gas_card_no'],
                $gasCardOrders['total_price'],
                $approvalData['approver_reason'],
            ]);
        }

        return $gasCardOrders;
    }

    /**
     * 油卡充值成功回调
     *
     * @param string $gasCardOrderUUID
     *
     * @return mixed
     * @throws ForbiddenException
     */
    public function rechargeSuccess($gasCardOrderUUID)
    {
        $statusList    = cons('truck.gas_card.order');
        $gasCardOrders = GasCardOrder::where('gas_card_order_uuid', $gasCardOrderUUID)->firstOrFail();
        if ($gasCardOrders->status != $statusList['waiting_recharge']) {
            throw new ForbiddenException('油卡充值订单状态错误');
        }
        $gasCardOrders->fill(['status' => $statusList['recharge_success']])->save();
        return $gasCardOrders;
    }
}