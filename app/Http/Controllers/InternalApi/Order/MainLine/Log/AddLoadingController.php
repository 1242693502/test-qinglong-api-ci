<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateAddLoadingRequest;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\AddLoadingService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;

/**
 * Class AddLoadingController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class AddLoadingController extends Controller
{
    /**
     * 添加多点装货地
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateAddLoadingRequest    $request
     * @param                                                                              $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateAddLoadingRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new AddLoadingService())->create($orderUUID, $driverUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }
}
