<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateCountUnloadingBeginRequest;
use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\CountUnloadingBeginService;
use Illuminate\Support\Arr;

/**
 * Class CountUnloadingBeginController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class CountUnloadingBeginController extends BaseController
{
    /**
     * 记录卸货开始时间
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateCountUnloadingBeginRequest $request
     * @param                                                                                    $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateCountUnloadingBeginRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new CountUnloadingBeginService())->create($orderUUID, $driverUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }
}
