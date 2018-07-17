<?php

namespace App\Http\Controllers\InternalApi\Truck\Log;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Truck\Log\CreatePenaltyRequest;
use App\Http\Resources\InternalApi\Truck\Log\PenaltyResource;
use App\Services\Truck\Log\PenaltyService;

/**
 * 车辆罚款记录
 * Class PenaltyController
 *
 * @package App\Http\Controllers\InternalApi\Truck\Log
 */
class PenaltyController extends BaseController
{
    /**
     * 添加车辆罚款记录
     *
     * @param \App\Http\Requests\InternalApi\Truck\Log\CreatePenaltyRequest $request
     *
     * @return \App\Http\Resources\InternalApi\Truck\Log\PenaltyResource
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public function store(CreatePenaltyRequest $request)
    {
        $attributes = $request->validated();
        $driverUUID = array_pull($attributes, 'driver_uuid');

        $truckPenaltyLog = (new PenaltyService)->create($driverUUID, $attributes);
        return new PenaltyResource($truckPenaltyLog);
    }
}