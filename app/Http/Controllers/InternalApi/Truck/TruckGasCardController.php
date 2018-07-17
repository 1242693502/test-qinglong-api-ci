<?php

namespace App\Http\Controllers\InternalApi\Truck;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Truck\GasCard\LossRequest;
use App\Http\Requests\InternalApi\Truck\GasCard\StoreRequest;
use App\Http\Requests\InternalApi\Truck\GasCard\UnbindRequest;
use App\Http\Resources\InternalApi\Truck\TruckGasCardResource;
use App\Services\Truck\TruckGasCardService;

class TruckGasCardController extends BaseController
{
    /**
     * 车辆关联油卡
     *
     * @param StoreRequest $request
     * @param string       $truckUUID
     *
     * @return \App\Http\Resources\InternalApi\Truck\TruckGasCardResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public function store(StoreRequest $request, $truckUUID)
    {
        $inputs        = $request->validated();
        $gasCardNo     = $inputs['gas_card_no'];
        $truckGasCards = (new TruckGasCardService())->bindGasCard($truckUUID, $gasCardNo);
        return new TruckGasCardResource($truckGasCards);
    }

    /**
     * 挂失油卡
     *
     * @param LossRequest $request
     * @param string      $gasCardNo
     *
     * @return \App\Http\Resources\InternalApi\Truck\TruckGasCardResource
     * @throws \Urland\Exceptions\Client\ForbiddenException
     */
    public function loss(LossRequest $request, $gasCardNo)
    {
        $inputs        = $request->validated();
        $reason        = $inputs['loss_reason'];
        $truckGasCards = (new TruckGasCardService())->loss($gasCardNo, $reason);
        return new TruckGasCardResource($truckGasCards);
    }

    /**
     * 解绑油卡
     *
     * @param UnbindRequest $request
     * @param string        $gasCardNo
     *
     * @return \App\Http\Resources\InternalApi\Truck\TruckGasCardResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     */
    public function unbind(UnbindRequest $request, $gasCardNo)
    {
        $inputs        = $request->validated();
        $reason        = $inputs['unbind_reason'];
        $truckGasCards = (new TruckGasCardService())->unbind($gasCardNo, $reason);
        return new TruckGasCardResource($truckGasCards);
    }
}