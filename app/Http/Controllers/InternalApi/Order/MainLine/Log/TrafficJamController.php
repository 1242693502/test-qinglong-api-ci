<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateTrafficJamRequest;
use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\TrafficJamService;
use Illuminate\Support\Arr;

/**
 * Class TrafficJamController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class TrafficJamController extends BaseController
{
    /**
     * 记录堵车
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateTrafficJamRequest        $request
     * @param                                                                                  $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateTrafficJamRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new TrafficJamService())->create($orderUUID, $driverUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }

}
