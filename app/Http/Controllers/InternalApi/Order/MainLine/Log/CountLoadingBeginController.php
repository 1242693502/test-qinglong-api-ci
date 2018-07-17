<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateCountLoadingBeginRequest;
use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\CountLoadingBeginService;
use Illuminate\Support\Arr;

/**
 * Class CountLoadingBeginController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class CountLoadingBeginController extends BaseController
{
    /**
     * 记录装货开始时间
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateCountLoadingBeginRequest $request
     * @param                                                                                  $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateCountLoadingBeginRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new CountLoadingBeginService())->create($orderUUID, $driverUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }

}
