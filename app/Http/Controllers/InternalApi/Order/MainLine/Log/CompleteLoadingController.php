<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateCompleteLoadingRequest;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Http\Controllers\Controller;
use App\Services\Order\MainLine\Log\CompleteLoadingService;
use Illuminate\Support\Arr;

/**
 * Class CompleteUnloadingController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class CompleteLoadingController extends Controller
{
    /**
     * 装货完成
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateCompleteLoadingRequest   $request
     * @param                                                                                  $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateCompleteLoadingRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new CompleteLoadingService())->create($orderUUID, $driverUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }
}
