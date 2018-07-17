<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Services\Order\MainLine\Log\HighWayLeaveService;
use Tests\TestCase;

class HighWayLeaveTest extends TestCase
{
    /**
     * 测试离开高速
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testHighWayLeave($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];
        $inputs      = [
            'remark' => '测试离开高速',
            'images' => [1111, 2222, 3333],
        ];

        (new HighWayLeaveService())->create($orderUUID, $driverUUID, $inputs);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.high_way_leave'),
            'title'       => cons()->lang('order.mainline.log.type.high_way_leave'),
            'description' => '记录离开高速',
            'remark'      => '测试离开高速',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.high_way_leave'))
            ->first(['contents']);

        $contents = [
            'images' => [1111, 2222, 3333],
        ];
        $this->assertArraySubset($contents, $log->contents);
    }
}
