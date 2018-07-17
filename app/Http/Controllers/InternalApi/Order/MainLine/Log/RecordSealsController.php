<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateRecordSealsRequest;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\RecordSealsService;
use Illuminate\Support\Arr;

/**
 * Class RecordSealsController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class RecordSealsController extends BaseController
{
    /**
     * 录封签号
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateRecordSealsRequest $request
     * @param                                                                            $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateRecordSealsRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new RecordSealsService())->create($orderUUID, $driverUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }
}
