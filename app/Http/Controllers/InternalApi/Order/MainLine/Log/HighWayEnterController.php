<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateHighWayEnterRequest;
use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\HighWayEnterService;
use Illuminate\Support\Arr;

/**
 * Class HighWayEnterController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class HighWayEnterController extends BaseController
{
    /**
     * 记录进入高速
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateHighWayEnterRequest      $request
     * @param                                                                                  $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateHighWayEnterRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new HighWayEnterService())->create($orderUUID, $driverUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }

}
