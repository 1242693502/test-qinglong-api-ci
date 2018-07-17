<?php

namespace App\Http\Controllers\InternalApi\Driver;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Driver\CreateRequest;
use App\Http\Requests\InternalApi\Driver\UpdateRequest;
use App\Http\Resources\InternalApi\Truck\TruckMileageResource;
use App\Http\Resources\InternalApi\Driver\DriverResource;
use App\Http\Resources\InternalApi\Driver\DriverTruckResource;
use App\Models\Driver\DriverTruck;
use App\Services\Driver\DriverService;
use App\Services\Truck\TruckService;
use Urland\Exceptions\Client;

/**
 * 司机接口类
 * Class DriverController
 *
 * @package App\Http\Controllers\InternalApi\Driver
 */
class DriverController extends BaseController
{
    /**
     * 创建司机
     *
     * @param \App\Http\Requests\InternalApi\Driver\CreateRequest $request
     *
     * @return \App\Http\Resources\InternalApi\Driver\DriverResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \InvalidArgumentException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public function store(CreateRequest $request)
    {
        $driver = (new DriverService())->create($request->validated());

        return new DriverResource($driver);
    }

    /**
     * 更新司机信息
     *
     * @param \App\Http\Requests\InternalApi\Driver\UpdateRequest $request
     * @param string                                              $driverUUID
     *
     * @return \App\Http\Resources\InternalApi\Driver\DriverResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \InvalidArgumentException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function update(UpdateRequest $request, $driverUUID)
    {
        $driver = (new DriverService())->update($driverUUID, $request->validated());

        return new DriverResource($driver);
    }

    /**
     * 获取正在驾驶的车辆信息
     *
     * @param string $driverUUID
     *
     * @return \App\Http\Resources\InternalApi\Driver\DriverTruckResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public function drivingTruck($driverUUID)
    {
        $driverTruck = DriverTruck::where('driver_uuid', $driverUUID)->firstOrFail();
        if (empty($driverTruck->truck_uuid)) {
            throw new Client\NotFoundException('当前司机没有绑定车辆');
        }

        return new DriverTruckResource($driverTruck->load('truck'));
    }

    /**
     * 获取正在驾驶的车辆里程
     *
     * @param string $driverUUID
     *
     * @return \App\Http\Resources\InternalApi\Truck\TruckMileageResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \InvalidArgumentException
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public function drivingTruckMileage($driverUUID)
    {
        $driverTruck = DriverTruck::where('driver_uuid', $driverUUID)->firstOrFail();
        if (empty($driverTruck->truck_uuid)) {
            throw new Client\NotFoundException('当前司机没有绑定车辆');
        }

        $mileage = (new TruckService())->getMileage($driverTruck->truck->license_plate_number);
        return new TruckMileageResource(['mileage' => $mileage]);
    }
}