<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateUnloadingAbnormalRequest;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\UnloadingAbnormalService;
use Illuminate\Support\Arr;

/**
 * Class UnloadingAbnormalController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class UnloadingAbnormalController extends BaseController
{
    /**
     * 记录卸货异常
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateUnloadingAbnormalRequest $request
     * @param                                                                                  $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateUnloadingAbnormalRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new UnloadingAbnormalService())->create($orderUUID, $driverUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }

}