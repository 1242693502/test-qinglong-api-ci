<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateCountLoadingEndRequest;
use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\CountLoadingEndService;
use Illuminate\Support\Arr;

/**
 * Class CountLoadingEndController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class CountLoadingEndController extends BaseController
{
    /**
     * 记录装货结束时间
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateCountLoadingEndRequest $request
     * @param                                                                                $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateCountLoadingEndRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new CountLoadingEndService())->create($orderUUID, $driverUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }
}
