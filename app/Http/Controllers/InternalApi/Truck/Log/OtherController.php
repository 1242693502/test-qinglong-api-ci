<?php

namespace App\Http\Controllers\InternalApi\Truck\Log;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Truck\Log\CreateOtherRequest;
use App\Http\Resources\InternalApi\Truck\Log\OtherResource;
use App\Services\Truck\Log\OtherService;

/**
 * 车辆其他记录
 * Class OtherController
 *
 * @package App\Http\Controllers\InternalApi\Truck\Log
 */
class OtherController extends BaseController
{
    /**
     * 添加其他记录
     *
     * @param \App\Http\Requests\InternalApi\Truck\Log\CreateOtherRequest $request
     *
     * @return \App\Http\Resources\InternalApi\Truck\Log\OtherResource
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public function store(CreateOtherRequest $request)
    {
        $attributes = $request->validated();
        $driverUUID = array_pull($attributes, 'driver_uuid');

        $truckOtherLog = (new OtherService())->create($driverUUID, $attributes);

        return new OtherResource($truckOtherLog);
    }
}
