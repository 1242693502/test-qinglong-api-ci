<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Models\Order\MainLine\Place;
use App\Services\Order\MainLine\Log\UnloadingAbnormalService;
use Tests\TestCase;

class UnloadingAbnormalTest extends TestCase
{
    /**
     * 测试卸货异常
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testUnloadingAbnormal($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];

        $inputs = [
            'remark'      => '测试卸货异常',
            'description' => '卸货异常描述',
            'images'      => [1111, 2222, 3333],
        ];

        (new UnloadingAbnormalService())->create($orderUUID, $driverUUID, $inputs);

        $orderMainLinePlace = Place::where('order_uuid', $orderUUID)
            ->where('type', cons('order.mainline.place.type.unloading'))
            ->orderByDesc('arrival_time')->first();

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.unloading_abnormal'),
            'title'       => cons()->lang('order.mainline.log.type.unloading_abnormal'),
            'description' => '卸货异常，卸货地：' . $orderMainLinePlace->area_name . $orderMainLinePlace->address . ' 共包含照片：3张 异常描述：卸货异常描述',
            'remark'      => '测试卸货异常',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.unloading_abnormal'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '卸货异常照片1', 'code' => 1111],
            ['name' => '卸货异常照片2', 'code' => 2222],
            ['name' => '卸货异常照片3', 'code' => 3333],
        ];

        $contents = [
            'place_uuid'   => $orderMainLinePlace->place_uuid,
            'full_address' => $orderMainLinePlace->area_name . $orderMainLinePlace->address,
            'images'       => $inputs['images'],
        ];
        $this->assertArraySubset($images, $log->images);
        $this->assertArraySubset($contents, $log->contents);
    }
}
