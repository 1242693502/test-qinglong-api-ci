<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateCompleteRequest;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\CompleteService;
use Illuminate\Support\Arr;

class CompleteController extends BaseController
{
    /**
     * 记录运输完成
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateCompleteRequest $request
     * @param                                                                         $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateCompleteRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new CompleteService())->create($orderUUID, $driverUUID, $inputs);

        return new OrderMainLineLogResource($orderMainLineLog);
    }

}