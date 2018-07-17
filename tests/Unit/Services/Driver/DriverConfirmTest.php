<?php

namespace Tests\Unit\Services\Driver;


use App\Models\Driver\DriverTruck;
use App\Models\Order\MainLine\Log;
use App\Models\Order\OrderMainLine;
use App\Services\Order\MainLine\DriverService;
use Tests\TestCase;
use Urland\Exceptions\Client\BadRequestException;
use Urland\Exceptions\Client\ForbiddenException;
use Urland\Exceptions\Client\NotFoundException;
use Urland\Exceptions\Client\ValidationException;

class DriverConfirmTest extends TestCase
{
    /**
     * 测试司机确认接单操作失败
     *
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testDriverConfirmFail_01($truckDriverData)
    {
        $orderUUID   = '1111111111111';
        $driverUUIDS = $truckDriverData['driver_uuid'];

        $secondDriverUUID = $driverUUIDS[1];

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('订单不存在');
        (new DriverService())->driverConfirm($orderUUID, $secondDriverUUID);

        return true;
    }

    /**
     * 测试司机确认接单操作失败
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testDriverConfirmFail_02($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];

        $secondDriverUUID = $driverUUIDS[1];

        $orderMainLine->setAttribute('truck_uuid', '')->save();

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('订单未指派车辆');
        (new DriverService())->driverConfirm($orderUUID, $secondDriverUUID);

        return true;
    }

    /**
     * 测试司机确认接单操作失败
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testDriverConfirmFail_03($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = '1111111111111';

        $secondDriverUUID = $driverUUIDS[1];

        $orderMainLine->setAttribute('truck_uuid', $truckDriverData['truck_uuid'])->save();

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('接单失败，司机未绑定当前订单指定车辆');
        (new DriverService())->driverConfirm($orderUUID, $secondDriverUUID);

        return true;
    }

    /**
     * 测试司机确认接单操作失败
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testDriverConfirmFail_04($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];

        $secondDriverUUID = $driverUUIDS[1];

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('接单失败，司机未驾驶当前订单指定车辆');
        (new DriverService())->driverConfirm($orderUUID, $secondDriverUUID);

        return true;
    }

    /**
     * 测试副司机确认接单操作
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testViceDriverConfirm($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];

        $secondDriverUUID = $driverUUIDS[1];

        //副司机确认订单会抛出异常 接单失败，司机未驾驶当前订单指定车辆
        $this->expectException(\Urland\Exceptions\Client\ForbiddenException::class);
        (new DriverService())->driverConfirm($orderUUID, $secondDriverUUID);

    }

    /**
     * 测试主司机确认接单操作
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testMainDriverConfirm($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];

        $firstDriverUUID  = $driverUUIDS[0];
        $secondDriverUUID = $driverUUIDS[1];
        $thirdDriverUUID  = $driverUUIDS[2];

        $this->useFakeTruckLog();
        //主司机确认订单
        (new DriverService())->driverConfirm($orderUUID, $thirdDriverUUID);

        $orderMainLine = OrderMainLine::where('order_uuid', $orderUUID)->first();
        $driverTruck   = DriverTruck::where('driver_uuid', $thirdDriverUUID)
            ->where('truck_uuid', $orderMainLine->truck_uuid)->first();

        $orderMainlineLog = [
            'driver_uuid'  => $thirdDriverUUID,
            'truck_uuid'   => $orderMainLine->truck_uuid,
            'driver_name'  => $driverTruck->driver->name,
            'driver_phone' => $driverTruck->driver->phone,
        ];

        $description = '司机确认接单，接单司机：' . $driverTruck->driver->name . '（' . $driverTruck->driver->phone . '）';

        $log = Log::where('driver_uuid', $thirdDriverUUID)
            ->where('truck_uuid', $orderMainLine->truck_uuid)->first(['contents', 'description']);

        $this->assertEquals($description, $log->description);

        $this->assertArraySubset($orderMainlineLog, $log->contents);

        $orderMainLineDriverData = [
            'order_uuid'   => $orderUUID,
            'driver_uuid'  => $thirdDriverUUID,
            'driver_name'  => $driverTruck->driver->name,
            'driver_phone' => $driverTruck->driver->phone,
            'type'         => (string)cons('order.mainline.driver.type.confirm')
        ];
        $this->assertDatabaseHas('order_mainline_driver', $orderMainLineDriverData);

        $driverTruck = DriverTruck::where('driver_uuid', $secondDriverUUID)->where('truck_uuid',
            $orderMainLine->truck_uuid)->first();

        $orderMainLineDriverData = [
            'order_uuid'   => $orderUUID,
            'driver_uuid'  => $secondDriverUUID,
            'driver_name'  => $driverTruck->driver->name,
            'driver_phone' => $driverTruck->driver->phone,
            'type'         => (string)cons('order.mainline.driver.type.follow')
        ];
        $this->assertDatabaseHas('order_mainline_driver', $orderMainLineDriverData);

        $orderMainlineStatuse = [
            'order_uuid'   => $orderUUID,
            'order_status' => cons('order.mainline.status.driver_prepare'),
        ];
        $this->assertDatabaseHas('order_mainline_statuses', $orderMainlineStatuse);

        $orderMainlineData = [
            'id'           => $orderMainLine->id,
            'order_status' => cons('order.mainline.status.driver_prepare')
        ];
        $this->assertDatabaseHas('order_mainlines', $orderMainlineData);

        $truckStatus = [
            'truck_uuid'   => $orderMainLine->orderTruck->truck_uuid,
            'truck_status' => cons('truck.status.in_transit'),
        ];
        $this->assertDatabaseHas('truck_statuses', $truckStatus);

        $truck = [
            'id'           => $orderMainLine->orderTruck->id,
            'truck_status' => cons('truck.status.in_transit'),
        ];
        $this->assertDatabaseHas('trucks', $truck);
    }


}