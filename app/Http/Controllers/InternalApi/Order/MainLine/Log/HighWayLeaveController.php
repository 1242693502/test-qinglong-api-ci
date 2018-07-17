<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateHighWayLeaveRequest;
use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\HighWayLeaveService;
use Illuminate\Support\Arr;

/**
 * Class HighWayLeaveController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class HighWayLeaveController extends BaseController
{
    /**
     * 记录离开高速
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateHighWayLeaveRequest      $request
     * @param                                                                                  $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateHighWayLeaveRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new HighWayLeaveService())->create($orderUUID, $driverUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }

}
