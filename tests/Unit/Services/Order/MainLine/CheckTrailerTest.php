<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Services\Order\MainLine\Log\CheckTrailerService;
use Tests\TestCase;

class CheckTrailerTest extends TestCase
{
    /**
     * 测试检查挂车
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
    public function testCheckTrailer($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];
        $inputs      = [
            'remark'  => '挂车异常原因',
            'codes'   => [10, 11, 12],
            'images'  => [1111, 2222, 3333],
        ];

        (new CheckTrailerService())->checkTrailer($orderUUID, $driverUUID, $inputs);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.check_trailer'),
            'title'       => cons()->lang('order.mainline.log.type.check_trailer'),
            'description' => '挂车检查：异常 共包含照片：3张',
            'remark'      => '挂车异常原因',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.check_trailer'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '挂车异常照片1', 'code' => '1111'],
            ['name' => '挂车异常照片2', 'code' => '2222'],
            ['name' => '挂车异常照片3', 'code' => '3333'],
        ];
        $this->assertArraySubset($images, $log->images);

        $contents = [
            ['code' => 10, 'name' => '挂车外观检查'],
            ['code' => 11, 'name' => '车辆有无刮碰'],
            ['code' => 12, 'name' => '反光标志、牌照是否齐全'],
            'codes'   => [10, 11, 12],
            'images'  => [1111, 2222, 3333],
        ];
        $this->assertArraySubset($contents, $log->contents);
    }
}
