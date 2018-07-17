<?php

namespace App\Http\Controllers\InternalApi\Truck\Log;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Truck\Log\CreateTollRoadRequest;
use App\Http\Resources\InternalApi\Truck\Log\TollRoadResource;
use App\Services\Truck\Log\TollRoadService;

/**
 * 录入路桥费用
 * Class TollRoadController
 *
 * @package App\Http\Controllers\InternalApi\Truck\Log
 */
class TollRoadController extends BaseController
{
    /**
     * 录入路桥费用
     *
     * @param \App\Http\Requests\InternalApi\Truck\Log\CreateTollRoadRequest $request
     *
     * @return \App\Http\Resources\InternalApi\Truck\Log\TollRoadResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function store(CreateTollRoadRequest $request)
    {
        $attributes = $request->validated();
        $driverUUID = array_pull($attributes, 'driver_uuid');

        $truckTollRoadLog = (new TollRoadService)->create($driverUUID, $attributes);
        return new TollRoadResource($truckTollRoadLog);
    }
}