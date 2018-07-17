<?php

namespace Tests\Unit\Services\Driver;


use App\Models\Driver\DriverTruck;
use App\Models\Order\MainLine\Log;
use App\Services\Driver\DriverTruckService;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Urland\Exceptions\Server\InternalServerException;

class SwapDrivingTest extends TestCase
{
    use WithFaker;

    /**
     * 测试司机换班失败
     *
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @return bool
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testSwapDrivingFail_01($truckDriverData)
    {
        $truckUUID  = '1111111111111';
        $driverUUID = $truckDriverData['driver_uuid'][0];
        $attributes = [
            'has_exceptions' => true,
            'images'         => [
                '12345678901234567890123456789012',
                '12345678901234567890123456789012',
                '12345678901234567890123456789012'
            ],
            'longitude'      => $this->faker->longitude,
            'latitude'       => $this->faker->latitude,
            'remark'         => '测试司机换班',
        ];

        $this->expectException(InternalServerException::class);
        $this->expectExceptionMessage('该车辆没有关联司机');
        $driverTruck = (new DriverTruckService())->swapDriving($truckUUID, $driverUUID, $attributes);

        return true;
    }

    /**
     * 测试司机换班失败
     *
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @return bool
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testSwapDrivingFail_02($truckDriverData)
    {
        $truckUUID  = $truckDriverData['truck_uuid'];
        $driverUUID = '1111111111111';
        $attributes = [
            'has_exceptions' => true,
            'images'         => [
                '12345678901234567890123456789012',
                '12345678901234567890123456789012',
                '12345678901234567890123456789012'
            ],
            'longitude'      => $this->faker->longitude,
            'latitude'       => $this->faker->latitude,
            'remark'         => '测试司机换班',
        ];

        $this->expectException(InternalServerException::class);
        $this->expectExceptionMessage('该司机没有关联该车辆');
        $driverTruck = (new DriverTruckService())->swapDriving($truckUUID, $driverUUID, $attributes);

        return true;
    }

    /**
     * 测试司机换班失败
     *
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @return bool
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testSwapDrivingFail_03($truckDriverData)
    {
        $truckUUID  = $truckDriverData['truck_uuid'];
        $driverUUID = $truckDriverData['driver_uuid'][2];
        $attributes = [
            'has_exceptions' => true,
            'images'         => [
                '12345678901234567890123456789012',
                '12345678901234567890123456789012',
                '12345678901234567890123456789012'
            ],
            'longitude'      => $this->faker->longitude,
            'latitude'       => $this->faker->latitude,
            'remark'         => '测试司机换班',
        ];

        $this->expectException(InternalServerException::class);
        $this->expectExceptionMessage('仅副司机允许提交换班');
        $driverTruck = (new DriverTruckService())->swapDriving($truckUUID, $driverUUID, $attributes);

        return true;
    }

    /**
     * 测试司机换班
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testSwapDriving($orderMainLine, $truckDriverData)
    {
        $orderUUID  = $orderMainLine->order_uuid;
        $truckUUID  = $truckDriverData['truck_uuid'];
        $driverUUID = $truckDriverData['driver_uuid'][0];
        $attributes = [
            'has_exceptions' => true,
            'images'         => [
                '12345678901234567890123456789012',
                '12345678901234567890123456789012',
                '12345678901234567890123456789012'
            ],
            'longitude'      => $this->faker->longitude,
            'latitude'       => $this->faker->latitude,
            'remark'         => '测试司机换班',
        ];

        $driverTrucks      = DriverTruck::where('truck_uuid', $truckUUID)->get();
        $originDriverTruck = $driverTrucks->where('is_driving', true)->first();

        $this->useFakeTruckLog();
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

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.swap_driving'),
            'title'       => cons()->lang('order.mainline.log.type.swap_driving'),
            'description' => '主副司机换班，原主司机：张三2（） 现主司机：张三（18888888888） 共包含照片：3 张 是否异常：是',
            'remark'      => '测试司机换班',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.swap_driving'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '换班异常照片1', 'code' => $attributes['images'][0]],
            ['name' => '换班异常照片2', 'code' => $attributes['images'][1]],
            ['name' => '换班异常照片3', 'code' => $attributes['images'][2]],
        ];
        $this->assertArraySubset($images, $log->images);

        $contents = [
            'has_exceptions'     => '1',
            'images'             => $attributes['images'],
            'driver_uuid'        => $driverUUID,
            'origin_driver_uuid' => $originDriverTruck->driver_uuid,
        ];

        $this->assertArraySubset($contents, $log->contents);

    }
}