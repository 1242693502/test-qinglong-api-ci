<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;


use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateNoCountLoadingRequest;
use App\Http\Resources\InternalApi\EmptyResource;
use App\Services\Order\MainLine\Log\NoCountLoadingService;
use Illuminate\Support\Arr;

/**
 * Class NoCountLoadingController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class NoCountLoadingController extends BaseController
{
    /**
     * 甩挂无需计时
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateNoCountLoadingRequest $request
     * @param                                                                               $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\EmptyResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateNoCountLoadingRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        (new NoCountLoadingService())->create($orderUUID, $driverUUID, $inputs);

        return new EmptyResource();
    }

}