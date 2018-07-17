<?php

namespace Tests\Unit\Services\Driver;

use App\Services\Driver\DriverTruckService;
use Tests\TestCase;
use Urland\Exceptions\Client\BadRequestException;
use Urland\Exceptions\Client\NotFoundException;

class AppointDriversTest extends TestCase
{

    /**
     * 测试司机绑定车辆
     *
     * @param $truck
     * @param $firstDriver
     * @param $secondDriver
     * @param $thirdDriver
     *
     * @depends Tests\Unit\Services\Truck\TruckTest::testCreate_1
     * @depends Tests\Unit\Services\Driver\DriverTest::testCreate_1
     * @depends Tests\Unit\Services\Driver\DriverTest::testCreate_2
     * @depends Tests\Unit\Services\Driver\DriverTest::testUpdate_1
     *
     * @return array
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testAppointDrivers($truck, $firstDriver, $secondDriver, $thirdDriver)
    {
        $truckUUID   = $truck->truck_uuid;
        $driverUUIDS = [
            $firstDriver->driver_uuid,
            $secondDriver->driver_uuid,
        ];

        (new DriverTruckService())->appointDrivers($truckUUID, $driverUUIDS, $thirdDriver->driver_uuid);

        $firstDriverTruckLog = [
            'driver_uuid' => $driverUUIDS[0],
            'is_driving'  => '0',
            'truck_uuid'  => $truckUUID,
            'remark'      => '司机绑定车辆',
        ];
        $this->assertDatabaseHas('driver_truck_logs', $firstDriverTruckLog);

        $secondDriverTruckLog = [
            'driver_uuid' => $driverUUIDS[1],
            'is_driving'  => '0',
            'truck_uuid'  => $truckUUID,
            'remark'      => '司机绑定车辆',
        ];
        $this->assertDatabaseHas('driver_truck_logs', $secondDriverTruckLog);

        $firstDriverTruck = [
            'driver_uuid' => $driverUUIDS[0],
            'truck_uuid'  => $truckUUID,
            'is_driving'  => '0',
        ];
        $this->assertDatabaseHas('driver_truck', $firstDriverTruck);

        $firstDriverTruck = [
            'driver_uuid' => $driverUUIDS[1],
            'truck_uuid'  => $truckUUID,
            'is_driving'  => '0',
        ];
        $this->assertDatabaseHas('driver_truck', $firstDriverTruck);

        $driverUUIDS = [
            $firstDriver->driver_uuid,
            $secondDriver->driver_uuid,
            $thirdDriver->driver_uuid
        ];

        return ['truck_uuid' => $truckUUID, 'driver_uuid' => $driverUUIDS];
    }

    /**
     * 测试司机绑定车辆失败
     *
     * @param $firstDriver
     * @param $secondDriver
     * @param $thirdDriver
     *
     * @depends Tests\Unit\Services\Driver\DriverTest::testCreate_1
     * @depends Tests\Unit\Services\Driver\DriverTest::testCreate_2
     * @depends Tests\Unit\Services\Driver\DriverTest::testUpdate_1
     *
     * @return bool
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testAppointDriversFail_01($firstDriver, $secondDriver, $thirdDriver)
    {
        $truckUUID   = '1111111111111';
        $driverUUIDS = [
            $firstDriver->driver_uuid,
            $secondDriver->driver_uuid,
        ];
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('获取车辆信息失败');
        (new DriverTruckService())->appointDrivers($truckUUID, $driverUUIDS, $thirdDriver->driver_uuid);

        return true;
    }

    /**
     * 测试司机绑定车辆失败
     *
     * @param $truck
     * @param $firstDriver
     * @param $secondDriver
     *
     * @depends Tests\Unit\Services\Truck\TruckTest::testCreate_1
     * @depends Tests\Unit\Services\Driver\DriverTest::testCreate_1
     * @depends Tests\Unit\Services\Driver\DriverTest::testCreate_2
     *
     * @return bool
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testAppointDriversFail_02($truck, $firstDriver, $secondDriver)
    {
        $truckUUID   = $truck->truck_uuid;
        $driverUUIDS = [
            $firstDriver->driver_uuid,
            $secondDriver->driver_uuid,
        ];
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('主副司机不能为同一人');
        (new DriverTruckService())->appointDrivers($truckUUID, $driverUUIDS, $secondDriver->driver_uuid);

        return true;
    }

    /**
     * 测试司机绑定车辆失败
     *
     * @param $truck
     * @param $firstDriver
     * @param $secondDriver
     *
     * @depends Tests\Unit\Services\Truck\TruckTest::testCreate_1
     * @depends Tests\Unit\Services\Driver\DriverTest::testCreate_1
     * @depends Tests\Unit\Services\Driver\DriverTest::testCreate_2
     *
     * @return bool
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testAppointDriversFail_03($truck, $firstDriver, $secondDriver)
    {
        $truckUUID      = $truck->truck_uuid;
        $driverUUIDS    = [
            $firstDriver->driver_uuid,
            $secondDriver->driver_uuid,
        ];
        $mainDriverUUID = '1111111111111';
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('获取主司机信息失败');
        (new DriverTruckService())->appointDrivers($truckUUID, $driverUUIDS, $mainDriverUUID);

        return true;
    }

}