<?php

namespace App\Http\Controllers\InternalApi\GasCard;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\GasCard\GasCardOrder\ApprovalRequest;
use App\Http\Requests\InternalApi\GasCard\GasCardOrder\StoreRequest;
use App\Http\Resources\InternalApi\EmptyResource;
use App\Http\Resources\InternalApi\GasCard\GasCardOrderResource;
use App\Services\GasCard\GasCardOrderService;

class GasCardOrderController extends BaseController
{

    /**
     * 申请油卡充值
     *
     * @param string       $gasCardNo
     * @param StoreRequest $request
     *
     * @return GasCardOrderResource
     */
    public function store($gasCardNo, StoreRequest $request)
    {
        $inputs        = $request->validated();
        $totalPrice    = $inputs['total_price'];
        $gasCardOrders = (new GasCardOrderService())->applyRecharge($gasCardNo, $totalPrice);
        return new GasCardOrderResource($gasCardOrders);
    }

    /**
     * 审核油卡充值申请
     *
     * @param string          $gasCardOrderUUID
     * @param ApprovalRequest $request
     *
     * @return GasCardOrderResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     */
    public function approval($gasCardOrderUUID, ApprovalRequest $request)
    {
        $inputs        = $request->validated();
        $gasCardOrders = (new GasCardOrderService())->approval($gasCardOrderUUID, $inputs);
        return new GasCardOrderResource($gasCardOrders);
    }

    /**
     * 油卡充值成功回调
     *
     * @param string $gasCardOrderUUID
     *
     * @return EmptyResource
     * @throws \Urland\Exceptions\Client\ForbiddenException
     */
    public function rechargeSuccess($gasCardOrderUUID)
    {
        (new GasCardOrderService())->rechargeSuccess($gasCardOrderUUID);
        return new EmptyResource();
    }
}