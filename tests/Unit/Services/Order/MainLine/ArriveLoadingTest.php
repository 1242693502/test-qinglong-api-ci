<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Models\Order\MainLine\Place;
use App\Services\Order\MainLine\Log\ArriveLoadingService;
use Tests\TestCase;

class ArriveLoadingTest extends TestCase
{
    /**
     * 测试到达装货地
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
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testArriveLoading($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];
        $place       = Place::where('order_uuid', $orderUUID)
            ->where('type', cons('order.mainline.place.type.loading'))
            ->whereNull('arrival_time')
            ->first(['place_uuid']);

        $placeUUID = $place->place_uuid;

        $inputs = [
            'remark'          => '测试到达装货地',
            'current_mileage' => '100000',
            'images'          => [1111, 2222, 3333],
        ];

        (new ArriveLoadingService())->create($orderUUID, $driverUUID, $placeUUID, $inputs);

        $orderMainLineStatus = [
            'order_uuid'   => $orderMainLine->order_uuid,
            'order_status' => cons('order.mainline.status.arrive_loading'),
        ];
        $this->assertDatabaseHas('order_mainline_statuses', $orderMainLineStatus);

        $orderMainLineData = [
            'id'           => $orderMainLine->id,
            'order_status' => cons('order.mainline.status.arrive_loading'),
        ];
        $this->assertDatabaseHas('order_mainlines', $orderMainLineData);

        $place = Place::where('order_uuid', $orderUUID)
            ->where('type', cons('order.mainline.place.type.loading'))
            ->first(['place_uuid', 'arrival_time', 'area_name', 'address']);

        $this->assertNotNull($place->arrival_time);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.arrive_loading'),
            'title'       => cons()->lang('order.mainline.log.type.arrive_loading'),
            'description' => '到达装货地：' . $place->area_name . $place->address . ' 当前里程：100公里',
            'remark'      => '测试到达装货地',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.arrive_loading'))
            ->first(['contents']);

        $contents = [
            'place_uuid'   => $place->place_uuid,
            'full_address' => $place->area_name . $place->address,
            'images'       => [1111, 2222, 3333],
        ];
        $this->assertArraySubset($contents, $log->contents);
    }
}
