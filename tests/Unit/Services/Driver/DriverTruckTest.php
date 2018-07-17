<?php

namespace Tests\Unit\Services\Driver;

use App\Models\Driver\Driver;
use App\Models\Driver\DriverTruck;
use App\Models\Truck\Truck;
use App\Services\Driver\DriverTruckService;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DriverTruckTest extends TestCase
{
    use WithFaker;

    /**
     * 测试司机绑定车辆
     *
     * @return array
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testAppointDrivers()
    {
        $truck        = factory(Truck::class)->create();
        $firstDriver  = factory(Driver::class)->create();
        $secondDriver = factory(Driver::class)->create();

        $truckUUID   = $truck->truck_uuid;
        $driverUUIDS = [
            $firstDriver->driver_uuid,
            $secondDriver->driver_uuid,
        ];

        (new DriverTruckService())->appointDrivers($truckUUID, $driverUUIDS);

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

        return ['truck_uuid' => $truckUUID, 'driver_uuid' => $driverUUIDS];
    }

    /**
     * 测试司机换班
     *
     * @param $truckDriverData
     *
     * @depends testAppointDrivers
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testSwapDriving($truckDriverData)
    {
        $truckUUID  = $truckDriverData['truck_uuid'];
        $driverUUID = $truckDriverData['driver_uuid'][0];
        $attributes = [
            'has_exceptions' => $this->faker->boolean,
            'images'         => ['12345678901234567890123456789012', '12345678901234567890123456789012', '12345678901234567890123456789012'],
            'longitude'      => $this->faker->longitude,
            'latitude'       => $this->faker->latitude,
            'remark'         => $this->faker->sentence,
        ];

        $driverTruck = (new DriverTruckService())->swapDriving($truckUUID, $driverUUID, $attributes);

        $this->assertTrue($driverTruck instanceof DriverTruck);

        $driverTruck = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'is_driving'  => '1',
        ];
        $this->assertDatabaseHas('driver_truck', $driverTruck);

        $driverTruckLog = [
            'driver_uuid' => $driverUUID,
            'is_driving'  => '1',
            'truck_uuid'  => $truckUUID,
            'remark'      => '副司机切换到主司机',
        ];
        $this->assertDatabaseHas('driver_truck_logs', $driverTruckLog);
    }
}