<?php

namespace App\Http\Controllers\InternalApi\Truck\Log;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Truck\Log\CreateRefuelRequest;
use App\Http\Resources\InternalApi\Truck\Log\RefuelResource;
use App\Services\Truck\Log\RefuelService;

/**
 * 车辆成本-加油记录
 *
 * Class RefuelController
 * @package App\Http\Controllers\InternalApi\Truck\Log
 */
class RefuelController extends BaseController
{
    /**
     * 添加成本-加油记录
     *
     * @param \App\Http\Requests\InternalApi\Truck\Log\CreateRefuelRequest $request
     *
     * @return \App\Http\Resources\InternalApi\Truck\Log\RefuelResource
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public function store(CreateRefuelRequest $request)
    {
        $attributes = $request->validated();
        $driverUUID = array_pull($attributes, 'driver_uuid');

        $truckRefuelLog = (new RefuelService())->create($driverUUID, $attributes);

        return new RefuelResource($truckRefuelLog);

    }
}
