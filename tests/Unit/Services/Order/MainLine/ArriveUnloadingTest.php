<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Models\Order\MainLine\Place;
use App\Services\Order\MainLine\Log\ArriveUnloadingService;
use Tests\TestCase;

class ArriveUnloadingTest extends TestCase
{
    /**
     * 测试到达卸货地
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testArriveUnloading($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];

        $orderMainLinePlace = Place::where('order_uuid', $orderUUID)
            ->where('type', cons('order.mainline.place.type.unloading'))
            ->whereNull('arrival_time')
            ->first(['place_uuid']);

        $placeUUID = $orderMainLinePlace->place_uuid;

        $inputs = [
            'remark'          => '测试到达卸货地',
            'address'         => '测试地址',
            'current_mileage' => '100000',
            'images'          => [1111, 2222, 3333],
        ];

        (new ArriveUnloadingService())->create($orderUUID, $driverUUID, $placeUUID, $inputs);

        $orderMainLineStatus = [
            'order_uuid'   => $orderUUID,
            'order_status' => cons('order.mainline.status.arrive_unloading'),
        ];
        $this->assertDatabaseHas('order_mainline_statuses', $orderMainLineStatus);

        $orderMainLineData = [
            'id'           => $orderMainLine->id,
            'order_status' => cons('order.mainline.status.arrive_unloading'),
        ];
        $this->assertDatabaseHas('order_mainlines', $orderMainLineData);

        $place = Place::where('order_uuid', $orderUUID)
            ->where('type', cons('order.mainline.place.type.unloading'))
            ->first(['place_uuid', 'area_name', 'address', 'arrival_time']);

        $this->assertNotNull($place->arrival_time);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.arrive_unloading'),
            'title'       => cons()->lang('order.mainline.log.type.arrive_unloading'),
            'description' => '到达卸货地：' . $place->area_name . $place->address . ' 当前里程：100公里',
            'remark'      => '测试到达卸货地',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.arrive_unloading'))
            ->first(['contents']);

        $contents = [
            'place_uuid'   => $place->place_uuid,
            'address'      => $inputs['address'],
            'images'       => $inputs['images'],
            'full_address' => $place->area_name . $place->address,
        ];
        $this->assertArraySubset($contents, $log->contents);
    }
}
