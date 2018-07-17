<?php

namespace App\Http\Controllers\InternalApi\Truck\Log;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Truck\Log\CreateParkRequest;
use App\Http\Resources\InternalApi\Truck\Log\ParkResource;
use App\Services\Truck\Log\ParkService;

/**
 *  车辆停车记录
 *
 * Class ParkController
 *
 * @package App\Http\Controllers\InternalApi\Truck\Log
 */
class ParkController extends BaseController
{
    /**
     * 添加停车记录
     *
     * @param \App\Http\Requests\InternalApi\Truck\Log\CreateParkRequest $request
     *
     * @return \App\Http\Resources\InternalApi\Truck\Log\ParkResource
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public function store(CreateParkRequest $request)
    {
        $attributes = $request->validated();
        $driverUUID = array_pull($attributes, 'driver_uuid');

        $truckParkLog = (new ParkService())->create($driverUUID, $attributes);
        return new ParkResource($truckParkLog);
    }
}
