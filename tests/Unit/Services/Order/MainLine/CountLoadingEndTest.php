<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Models\Order\MainLine\Place;
use App\Services\Order\MainLine\ActionService;
use App\Services\Order\MainLine\Log\CountLoadingEndService;
use Tests\TestCase;

class CountLoadingEndTest extends TestCase
{
    /**
     * 测试装货计时结束
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
    public function testCountLoadingEnd($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];

        $inputs = [
            'remark' => '测试装货计时结束',
        ];

        (new CountLoadingEndService())->create($orderUUID, $driverUUID, $inputs);

        $orderMainLinePlace = Place::where('order_uuid', $orderUUID)
            ->where('type', cons('order.mainline.place.type.loading'))
            ->orderByDesc('arrival_time')->first();

        $this->assertNotNull($orderMainLinePlace->count_end_time);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.count_loading_end'),
            'title'       => cons()->lang('order.mainline.log.type.count_loading_end'),
            'description' => '装货计时结束，装货地：' . $orderMainLinePlace->area_name . $orderMainLinePlace->address,
            'remark'      => '测试装货计时结束',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.count_loading_end'))
            ->first(['contents']);

        $contents = [
            'order_uuid'   => $orderUUID,
            'driver_uuid'  => $driverUUID,
            'truck_uuid'   => $truckUUID,
            'place_uuid'   => $orderMainLinePlace->place_uuid,
            'full_address' => $orderMainLinePlace->area_name . $orderMainLinePlace->address,
        ];
        $this->assertArraySubset($contents, $log->contents);

        $this->assertTrue(ActionService::serviceForOrderUUID($orderUUID)->stage()->isActionDone('count_loading_end'));
    }
}
