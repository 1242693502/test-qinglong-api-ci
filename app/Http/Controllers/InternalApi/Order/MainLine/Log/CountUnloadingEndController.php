<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateCountUnloadingEndRequest;
use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\CountUnloadingEndService;
use Illuminate\Support\Arr;

/**
 * Class CountUnloadingEndController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class CountUnloadingEndController extends BaseController
{
    /**
     * 记录卸货结束时间
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateCountUnloadingEndRequest $request
     * @param                                                                                  $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateCountUnloadingEndRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new CountUnloadingEndService())->create($orderUUID, $driverUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }
}
