<?php

namespace App\Http\Controllers\InternalApi\Truck;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Truck;
use App\Http\Resources\InternalApi\Driver\DriverTruckResource;
use App\Http\Resources\InternalApi\EmptyResource;
use App\Http\Resources\InternalApi\Truck\TruckResource;
use App\Models\Driver\DriverTruck;
use App\Services\Driver\DriverTruckService;
use App\Services\Truck\TruckService;
use Illuminate\Support\Arr;

/**
 * 车辆接口类
 * Class DriverController
 *
 * @package App\Http\Controllers\InternalApi\Driver
 */
class TruckController extends BaseController
{
    /**
     * 创建车辆
     *
     * @param \App\Http\Requests\InternalApi\Truck\CreateRequest $request
     *
     * @return \App\Http\Resources\InternalApi\Truck\TruckResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Urland\Exceptions\Client\ValidationException
     */
    public function store(Truck\CreateRequest $request)
    {
        $truck = (new TruckService)->create($request->validated());

        return new TruckResource($truck);
    }

    /**
     * 更新车辆信息
     *
     * @param \App\Http\Requests\InternalApi\Truck\UpdateRequest $request
     * @param string                                             $truckUUID
     *
     * @return \App\Http\Resources\InternalApi\Truck\TruckResource
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Urland\Exceptions\Client\ValidationException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function update(Truck\UpdateRequest $request, $truckUUID)
    {
        $truck = (new TruckService)->update($truckUUID, $request->validated());

        return new TruckResource($truck);
    }

    /**
     * 获取车辆绑定的司机记录
     *
     * @param string $truckUUID
     *
     * @return \App\Http\Resources\InternalApi\Driver\DriverTruckResource[]
     */
    public function showAppointedDrivers($truckUUID)
    {
        $driverTruck = DriverTruck::where('truck_uuid', $truckUUID)->orderBy('is_driving', 'desc')->get();
        return DriverTruckResource::collection($driverTruck->load('driver'));
    }


    /**
     * 司机绑定车辆
     *
     * @param \App\Http\Requests\InternalApi\Truck\AppointDriverRequest $request
     * @param string                                                    $truckUUID
     *
     * @return \App\Http\Resources\InternalApi\EmptyResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function appointDrivers(Truck\AppointDriverRequest $request, $truckUUID)
    {
        /*
         * driverUUIDs参数数组 [1, 2, 3]
         *
         * 调用TruckService@appointDrivers
         *
         * @return EmptyResource
         */
        $inputs         = $request->validated();
        $driverUUIDs    = $inputs['other_driver_uuid_list'];
        $mainDriverUUID = $inputs['main_driver_uuid'];

        (new DriverTruckService())->appointDrivers($truckUUID, $driverUUIDs, $mainDriverUUID);
        return new EmptyResource();
    }

    /**
     * 副司机切换到主司机（主副司机换班）
     *
     * @param \App\Http\Requests\InternalApi\Truck\SwapDrivingRequest $request
     * @param string                                                  $truckUUID
     *
     * @return \App\Http\Resources\InternalApi\Driver\DriverTruckResource
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function swapDriving(Truck\SwapDrivingRequest $request, $truckUUID)
    {
        $attributes  = $request->validated();
        $driverUUID  = Arr::pull($attributes, 'driver_uuid');
        $driverTruck = (new DriverTruckService())->swapDriving($truckUUID, $driverUUID, $attributes);

        return new DriverTruckResource($driverTruck);
    }

    /**
     * 清空司机绑定车辆记录
     *
     * @param $truckUUID
     *
     * @return \App\Http\Resources\InternalApi\EmptyResource
     */
    public function removeDrivers($truckUUID)
    {
        /*
         * 此方法不需要参数
         *
         * 调用TruckService@removeDrivers
         *
         * @return EmptyResource
         */
        (new DriverTruckService())->removeDriversByTruckUUID($truckUUID);
        return new EmptyResource();
    }

    /**
     * 获取某车辆正在驾驶的司机信息
     *
     * @param string $truckUUID
     *
     * @return \App\Http\Resources\InternalApi\Driver\DriverTruckResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function drivingDriver($truckUUID)
    {
        $driverTruck = DriverTruck::where('truck_uuid', $truckUUID)->where('is_driving', 1)->firstOrFail();
        return new DriverTruckResource($driverTruck->load('driver'));
    }
}