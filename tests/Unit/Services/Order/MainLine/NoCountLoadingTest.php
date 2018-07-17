<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Models\Order\MainLine\Place;
use App\Services\Order\MainLine\ActionService;
use App\Services\Order\MainLine\Log\NoCountLoadingService;
use Tests\TestCase;

class NoCountLoadingTest extends TestCase
{
    /**
     * 测试甩挂无需计时
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
    public function testNoCountLoading($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];

        $orderMainLinePlace = Place::where('order_uuid', $orderUUID)
            ->where('type', cons('order.mainline.place.type.loading'))
            ->orderByDesc('arrival_time')->first();

        $inputs = [
            'remark'                => '测试甩挂无需计时',
            'area_code'             => '110101001',
            'address'               => '测试地址',
            'address_contact_name'  => '李四',
            'address_contact_phone' => '13428281899',
            'images'                => [1111, 2222, 3333],
        ];

        (new NoCountLoadingService())->create($orderUUID, $driverUUID, $inputs);

        $place = Place::where('order_uuid', $orderUUID)
            ->where('type', cons('order.mainline.place.type.loading'))
            ->orderByDesc('arrival_time')->first(['count_begin_time', 'count_end_time']);

        $this->assertNotNull($place->count_begin_time);
        $this->assertNotNull($place->count_end_time);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.count_loading_end'),
            'title'       => cons()->lang('order.mainline.log.type.count_loading_end'),
            'description' => '甩挂无需计时，装货地：' . $orderMainLinePlace->area_name . $orderMainLinePlace->address,
            'remark'      => '测试甩挂无需计时',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.count_loading_end'))
            ->orderByDesc('id')
            ->first(['contents']);

        $contents = [
            'place_uuid'            => $orderMainLinePlace->place_uuid,
            'full_address'          => $orderMainLinePlace->area_name . $orderMainLinePlace->address,
            'area_code'             => $inputs['area_code'],
            'address'               => $inputs['address'],
            'address_contact_name'  => $inputs['address_contact_name'],
            'address_contact_phone' => $inputs['address_contact_phone'],
            'images'                => $inputs['images'],
        ];
        $this->assertArraySubset($contents, $log->contents);

        $this->assertTrue(ActionService::serviceForOrderUUID($orderUUID)->stage()->isActionDone('count_loading_begin'));
    }
}
