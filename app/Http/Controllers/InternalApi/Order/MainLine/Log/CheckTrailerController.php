<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Requests\InternalApi\Order\MainLine\Log;
use App\Http\Requests\InternalApi\Order\MainLine\Log\CheckTrailerCertificatesRequest;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\CheckTrailerService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;

/**
 * Class CheckTrailerController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class CheckTrailerController extends Controller
{
    /**
     * 挂车证件检查
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CheckTrailerCertificatesRequest $request
     * @param                                                                                   $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CheckTrailerCertificatesRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new CheckTrailerService())->create($orderUUID, $driverUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }

    /**
     * 检查挂车
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CheckTrailerRequest $request
     * @param                                                                       $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function checkTrailer(Log\CheckTrailerRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new CheckTrailerService())->checkTrailer($orderUUID, $driverUUID, $inputs);

        return new OrderMainLineLogResource($orderMainLineLog);
    }
}
