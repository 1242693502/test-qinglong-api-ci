<?php

namespace App\Http\Controllers\InternalApi\Truck\Log;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Truck\Log\CreateAdBlueRequest;
use App\Http\Resources\InternalApi\Truck\Log\AdBlueResource;
use App\Services\Truck\Log\AdBlueService;
use Illuminate\Support\Arr;

/**
 * 车辆尿素记录
 * Class AdBlueController
 *
 * @package App\Http\Controllers\InternalApi\Truck\Log
 */
class AdBlueController extends BaseController
{
    /**
     * 添加尿素记录
     *
     * @param \App\Http\Requests\InternalApi\Truck\Log\CreateAdBlueRequest $request
     *
     * @return \App\Http\Resources\InternalApi\Truck\Log\AdBlueResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function store(CreateAdBlueRequest $request)
    {
        $attributes = $request->validated();
        $driverUUID = Arr::pull($attributes, 'driver_uuid');

        $truckAdBlueLog = (new AdBlueService)->create($driverUUID, $attributes);
        return new AdBlueResource($truckAdBlueLog);
    }
}