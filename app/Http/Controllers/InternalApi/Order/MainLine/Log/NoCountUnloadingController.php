<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;


use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateNoCountUnloadingRequest;
use App\Http\Resources\InternalApi\EmptyResource;
use App\Services\Order\MainLine\Log\NoCountUnloadingService;
use Illuminate\Support\Arr;

/**
 * Class NoCountUnloadingController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class NoCountUnloadingController extends BaseController
{
    /**
     * 甩挂无需计时
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateNoCountUnloadingRequest $request
     * @param                                                                                 $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\EmptyResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateNoCountUnloadingRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        (new NoCountUnloadingService())->create($orderUUID, $driverUUID, $inputs);

        return new EmptyResource();
    }
}