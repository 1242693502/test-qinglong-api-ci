<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Models\Order\MainLine\Place;
use App\Services\Order\MainLine\Log\CompleteLoadingService;
use Tests\TestCase;

class CompleteLoadingTest extends TestCase
{
    /**
     * 测试装货完成
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
    public function testCompleteLoading($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];

        $orderMainLinePlace = Place::where('order_uuid', $orderUUID)
            ->where('type', cons('order.mainline.place.type.loading'))
            ->orderByDesc('arrival_time')->first();

        $inputs = [
            'remark'  => '测试装货完成',
            'address' => '测试地址',
            'images'  => [1111, 2222, 3333],
        ];

        (new CompleteLoadingService())->create($orderUUID, $driverUUID, $inputs);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.complete_loading'),
            'title'       => cons()->lang('order.mainline.log.type.complete_loading'),
            'description' => '完成装货：' . $orderMainLinePlace->area_name . $orderMainLinePlace->address,
            'remark'      => '测试装货完成',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.complete_loading'))
            ->first(['contents']);

        $contents = [
            'place_uuid'   => $orderMainLinePlace->place_uuid,
            'full_address' => $orderMainLinePlace->area_name . $orderMainLinePlace->address,
        ];
        $this->assertArraySubset($contents, $log->contents);

        $place = Place::where('order_uuid', $orderUUID)
            ->where('type', cons('order.mainline.place.type.loading'))
            ->orderByDesc('arrival_time')->first(['departure_time']);

        $this->assertNotNull($place->departure_time);

        $orderMainLineStatus = [
            'order_uuid'   => $orderUUID,
            'order_status' => cons('order.mainline.status.in_transit'),
        ];
        $this->assertDatabaseHas('order_mainline_statuses', $orderMainLineStatus);

        $orderMainLineData = [
            'id'           => $orderMainLine->id,
            'order_status' => cons('order.mainline.status.in_transit'),
        ];
        $this->assertDatabaseHas('order_mainlines', $orderMainLineData);
    }
}
