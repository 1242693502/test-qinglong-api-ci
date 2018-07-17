<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateCompleteUnloadingRequest;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Http\Controllers\Controller;
use App\Services\Order\MainLine\Log\CompleteUnloadingService;
use Illuminate\Support\Arr;

/**
 * Class CompleteUnloadingController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class CompleteUnloadingController extends Controller
{
    /**
     * 卸货完成
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateCompleteUnloadingRequest $request
     * @param                                                                                  $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateCompleteUnloadingRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new CompleteUnloadingService())->create($orderUUID, $driverUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }
}
