<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Models\Order\MainLine\Place;
use App\Services\Order\MainLine\Log\CountUnloadingEndService;
use Tests\TestCase;

class CountUnloadingEndTest extends TestCase
{
    /**
     * 测试卸货计时结束
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
    public function testCountUnloadingEnd($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];

        $inputs = [
            'remark' => '测试卸货计时结束',
        ];

        (new CountUnloadingEndService())->create($orderUUID, $driverUUID, $inputs);

        $orderMainLinePlace = Place::where('order_uuid', $orderUUID)
            ->where('type', cons('order.mainline.place.type.unloading'))
            ->orderByDesc('arrival_time')->first();

        $this->assertNotNull($orderMainLinePlace->count_end_time);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.count_unloading_end'),
            'title'       => cons()->lang('order.mainline.log.type.count_unloading_end'),
            'description' => '卸货计时结束，卸货地：' . $orderMainLinePlace->area_name . $orderMainLinePlace->address,
            'remark'      => '测试卸货计时结束',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.count_unloading_end'))
            ->first(['contents']);

        $contents = [
            'place_uuid'   => $orderMainLinePlace->place_uuid,
            'full_address' => $orderMainLinePlace->area_name . $orderMainLinePlace->address,
        ];
        $this->assertArraySubset($contents, $log->contents);
    }
}
