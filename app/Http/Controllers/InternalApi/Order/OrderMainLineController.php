<?php

namespace App\Http\Controllers\InternalApi\Order;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Order\MainLine\AppointTruckRequest;
use App\Http\Requests\InternalApi\Order\MainLine\CreateRequest;
use App\Http\Requests\InternalApi\Order\MainLine\DriverConfirmRequest;
use App\Http\Resources\InternalApi\EmptyResource;
use App\Http\Resources\InternalApi\Order\OrderMainLineResource;
use App\Services\Order\MainLine;
use App\Services\Order\OrderMainLineService;

class OrderMainLineController extends BaseController
{
    /**
     * 创建订单
     *
     * @param CreateRequest $request
     *
     * @return OrderMainLineResource
     * @throws \InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateRequest $request)
    {
        $inputs = $request->validated();

        $orderMainLine = (new OrderMainLineService())->create($inputs);

        return new OrderMainLineResource($orderMainLine);
    }

    /**
     * 订单指派车辆
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\AppointTruckRequest $request
     * @param                                                                   $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\OrderMainLineResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function appointTruck(AppointTruckRequest $request, $orderUUID)
    {
        $truckUUID     = $request->input('truck_uuid');
        $orderMainLine = (new MainLine\TruckService())->appointTruck($orderUUID, $truckUUID);
        return new OrderMainLineResource($orderMainLine);
    }

    /**
     * 司机确认接单操作
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\DriverConfirmRequest $request
     * @param                                                                    $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\EmptyResource
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function driverConfirm(DriverConfirmRequest $request, $orderUUID)
    {
        $driverUUID = $request->input('driver_uuid');
        (new MainLine\DriverService())->driverConfirm($orderUUID, $driverUUID);
        return new EmptyResource();
    }
}
