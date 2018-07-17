<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateArriveUnloadingRequest;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\ArriveUnloadingService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;


/**
 * Class ArriveUnLoadingController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class ArriveUnLoadingController extends Controller
{
    /**
     * 到达卸货地
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateArriveUnloadingRequest $request
     * @param                                                                                $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateArriveUnloadingRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');
        $placeUUID  = Arr::pull($inputs, 'place_uuid');

        $orderMainLineLog = (new ArriveUnloadingService())->create($orderUUID, $driverUUID, $placeUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }
}
