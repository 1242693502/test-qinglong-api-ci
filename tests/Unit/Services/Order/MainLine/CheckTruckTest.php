<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Models\Order\OrderMainLine;
use App\Services\Order\MainLine\Log\CheckTruckService;
use Tests\TestCase;
use Urland\Exceptions\Client\BadRequestException;
use Urland\Exceptions\Client\ForbiddenException;

class CheckTruckTest extends TestCase
{
    /**
     * 测试检查车辆失败
     *
     * @param $orderMainLine
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testCheckTruckFail_01($orderMainLine)
    {
        $orderUUID  = $orderMainLine->order_uuid;
        $driverUUID = '1111111111111';
        $inputs     = [
            'remark' => '车辆异常原因',
            'codes'  => [10, 11, 12],
            'images' => [1111, 2222, 3333],
        ];
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('该司机无驾驶中的车辆');
        (new CheckTruckService())->checkTruck($orderUUID, $driverUUID, $inputs);

        return true;
    }

    /**
     * 测试检查车辆失败
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testCheckTruckFail_02($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];
        $inputs      = [
            'remark' => '车辆异常原因',
            'codes'  => [10, 11, 12],
            'images' => [1111, 2222, 3333],
        ];
        $orderMainLine->setAttribute('truck_uuid', '1111111111111')->save();
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('该车辆不能操作该订单');
        (new CheckTruckService())->checkTruck($orderUUID, $driverUUID, $inputs);

        return true;
    }

    /**
     * 测试检查车辆失败
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testCheckTruckFail_03($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];
        $inputs      = [
            'remark' => '车辆异常原因',
            'codes'  => [10, 11, 12],
            'images' => [1111, 2222, 3333],
        ];
        $orderMainLine->setAttribute('truck_uuid', $truckUUID)->save();
        $orderStatus = cons('order.mainline.status.cancel');
        $orderMainLine->setAttribute('order_status', $orderStatus)->save();
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('当前订单状态异常');
        (new CheckTruckService())->checkTruck($orderUUID, $driverUUID, $inputs);

        return true;
    }

    /**
     * 测试检查车辆
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testCheckTruck($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];
        $inputs      = [
            'remark' => '车辆异常原因',
            'codes'  => [10, 11, 12],
            'images' => [1111, 2222, 3333],
        ];

        $orderStatus = cons('order.mainline.status.driver_prepare');
        $orderMainLine->setAttribute('order_status', $orderStatus)->save();

        $this->useFakeTruckLog();
        (new CheckTruckService())->checkTruck($orderUUID, $driverUUID, $inputs);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.check_truck'),
            'title'       => cons()->lang('order.mainline.log.type.check_truck'),
            'description' => '车辆检查：异常 共包含照片：3张',
            'remark'      => '车辆异常原因',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.check_truck'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '车辆异常照片1', 'code' => '1111'],
            ['name' => '车辆异常照片2', 'code' => '2222'],
            ['name' => '车辆异常照片3', 'code' => '3333'],
        ];
        $this->assertArraySubset($images, $log->images);

        $contents = [
            ['code' => 10, 'name' => '车辆外观、工具检查'],
            ['code' => 11, 'name' => '车辆有无刮碰'],
            ['code' => 12, 'name' => '雨刮器、后视镜、牌照是否齐全'],
            'codes'  => [10, 11, 12],
            'images' => [1111, 2222, 3333],
        ];
        $this->assertArraySubset($contents, $log->contents);

        $orderMainLine = OrderMainLine::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->first();

        $orderMainlineStatuses = [
            'order_uuid'   => $orderMainLine->order_uuid,
            'order_status' => cons('order.mainline.status.in_transit'),
        ];
        $this->assertDatabaseHas('order_mainline_statuses', $orderMainlineStatuses);

        $orderMainlineData = [
            'id'           => $orderMainLine->id,
            'order_status' => cons('order.mainline.status.in_transit'),
        ];
        $this->assertDatabaseHas('order_mainlines', $orderMainlineData);
    }
}
