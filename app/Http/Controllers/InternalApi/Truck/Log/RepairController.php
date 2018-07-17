<?php

namespace App\Http\Controllers\InternalApi\Truck\Log;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Truck\Log\CreateRepairRequest;
use App\Http\Resources\InternalApi\Truck\Log\RepairResource;
use App\Services\Truck\Log\RepairService;

/**
 * 车辆维修保养记录
 * Class RepairController
 *
 * @package App\Http\Controllers\InternalApi\Truck\Log
 */
class RepairController extends BaseController
{
    /**
     * 添加维修保养记录
     *
     * @param \App\Http\Requests\InternalApi\Truck\Log\CreateRepairRequest $request
     *
     * @return \App\Http\Resources\InternalApi\Truck\Log\RepairResource
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public function store(CreateRepairRequest $request)
    {
        $attributes = $request->validated();
        $driverUUID = array_pull($attributes, 'driver_uuid');

        $truckRepairLog = (new RepairService())->create($driverUUID, $attributes);

        return new RepairResource($truckRepairLog);

    }
}
