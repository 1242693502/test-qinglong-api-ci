<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateRecordWeightRequest;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\RecordWeightService;
use Illuminate\Support\Arr;

/**
 * Class RecordWeightController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class RecordWeightController extends BaseController
{
    /**
     * 录过磅单
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateRecordWeightRequest $request
     * @param                                                                             $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateRecordWeightRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new RecordWeightService())->create($orderUUID, $driverUUID, $inputs);

        return new OrderMainLineLogResource($orderMainLineLog);
    }

}