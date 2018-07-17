<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Services\Order\MainLine\Log\TrafficJamService;
use Tests\TestCase;

class TrafficJamTest extends TestCase
{
    /**
     * 测试记录堵车
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testTrafficJam($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];
        $inputs      = [
            'remark' => '测试记录堵车',
            'images' => [1111, 2222, 3333],
        ];

        (new TrafficJamService())->create($orderUUID, $driverUUID, $inputs);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.traffic_jam'),
            'title'       => cons()->lang('order.mainline.log.type.traffic_jam'),
            'description' => '记录堵车，共包含照片：3张',
            'remark'      => '测试记录堵车',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.traffic_jam'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '堵车照片1', 'code' => '1111'],
            ['name' => '堵车照片2', 'code' => '2222'],
            ['name' => '堵车照片3', 'code' => '3333'],
        ];
        $this->assertArraySubset($images, $log->images);

        $contents = [
            'images' => [1111, 2222, 3333],
        ];
        $this->assertArraySubset($contents, $log->contents);
    }
}
