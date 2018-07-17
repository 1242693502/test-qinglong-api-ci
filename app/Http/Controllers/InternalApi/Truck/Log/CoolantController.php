<?php

namespace App\Http\Controllers\InternalApi\Truck\Log;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Truck\Log\CreateCoolantRequest;
use App\Http\Resources\InternalApi\Truck\Log\CoolantResource;
use App\Services\Truck\Log\CoolantService;

/**
 * 录入加水费用
 * Class CoolantController
 *
 * @package App\Http\Controllers\InternalApi\Truck\Log
 */
class CoolantController extends BaseController
{
    /**
     * 录入加水费用
     *
     * @param \App\Http\Requests\InternalApi\Truck\Log\CreateCoolantRequest $request
     *
     * @return \App\Http\Resources\InternalApi\Truck\Log\CoolantResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function store(CreateCoolantRequest $request)
    {
        $attributes = $request->validated();
        $driverUUID = array_pull($attributes, 'driver_uuid');

        $truckCoolantLog = (new CoolantService)->create($driverUUID, $attributes);
        return new CoolantResource($truckCoolantLog);
    }
}