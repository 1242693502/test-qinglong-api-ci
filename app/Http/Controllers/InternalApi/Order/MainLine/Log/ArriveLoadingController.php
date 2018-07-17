<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateArriveLoadingRequest;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\ArriveLoadingService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;

/**
 * Class ArriveLoadingController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class ArriveLoadingController extends Controller
{
    /**
     * 到达装货地
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateArriveLoadingRequest $request
     * @param                                                                              $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateArriveLoadingRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');
        $placeUUID  = Arr::pull($inputs, 'place_uuid');

        $orderMainLineLog = (new ArriveLoadingService())->create($orderUUID, $driverUUID, $placeUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }
}
