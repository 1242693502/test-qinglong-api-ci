<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Order\MainLine\Log;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\CheckTruckService;
use Illuminate\Support\Arr;

class CheckTruckController extends BaseController
{
    /**
     * 检查车辆证件
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CheckTruckCertificatesRequest $request
     * @param                                                                                 $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function checkTruckCertificates(Log\CheckTruckCertificatesRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new CheckTruckService())->checkTruckCertificates($orderUUID, $driverUUID, $inputs);

        return new OrderMainLineLogResource($orderMainLineLog);
    }

    /**
     * 检查车辆
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CheckTruckRequest $request
     * @param                                                                     $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function checkTruck(Log\CheckTruckRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new CheckTruckService())->checkTruck($orderUUID, $driverUUID, $inputs);

        return new OrderMainLineLogResource($orderMainLineLog);
    }

}