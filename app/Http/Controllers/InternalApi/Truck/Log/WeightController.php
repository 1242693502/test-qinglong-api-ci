<?php

namespace App\Http\Controllers\InternalApi\Truck\Log;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Truck\Log\CreateWeightRequest;
use App\Http\Resources\InternalApi\Truck\Log\OtherResource;
use App\Services\Truck\Log\WeightService;

/**
 * 录过磅单
 * Class WeightController
 *
 * @package App\Http\Controllers\InternalApi\Truck\Log
 */
class WeightController extends BaseController
{
    /**
     * 录过磅单
     *
     * @param \App\Http\Requests\InternalApi\Truck\Log\CreateWeightRequest $request
     *
     * @return \App\Http\Resources\InternalApi\Truck\Log\OtherResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateWeightRequest $request)
    {
        $attributes = $request->validated();
        $driverUUID = array_pull($attributes, 'driver_uuid');

        $truckWeightLog = (new WeightService())->create($driverUUID, $attributes);
        return new OtherResource($truckWeightLog);
    }
}