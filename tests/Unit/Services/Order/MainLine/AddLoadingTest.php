<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Models\Order\MainLine\Place;
use App\Services\Order\MainLine\Log\AddLoadingService;
use QingLong\Platform\Area\Area;
use Tests\TestCase;

class AddLoadingTest extends TestCase
{
    /**
     * 测试添加多点装货地
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testAddLoading($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];

        $orderMainLinePlace = Place::where('order_uuid', $orderUUID)
            ->where('type', cons('order.mainline.place.type.loading'))
            ->orderByDesc('arrival_time')->first();

        $inputs = [
            'remark'                => '测试添加多点装货地',
            'area_code'             => '110101001',
            'address'               => '测试地址',
            'address_contact_name'  => '李四',
            'address_contact_phone' => '13428281899',
            'images'                => [1111, 2222, 3333],
        ];

        (new AddLoadingService())->create($orderUUID, $driverUUID, $inputs);

        $areaInfo = app(Area::class)->getFinalInfo($inputs['area_code']);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.add_loading'),
            'title'       => cons()->lang('order.mainline.log.type.add_loading'),
            'description' => '添加装货地：' . $areaInfo['full_name'] . $inputs['address'],
            'remark'      => '测试添加多点装货地',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.add_loading'))
            ->first(['contents']);

        $contents = [
            'order_uuid'            => $orderUUID,
            'type'                  => cons('order.mainline.place.type.loading'),
            'address_contact_name'  => $orderMainLinePlace->address_contact_name,
            'address_contact_phone' => $orderMainLinePlace->address_contact_phone,
            'area_code'             => $inputs['area_code'],
            'address_contact_name'  => $inputs['address_contact_name'],
            'address_contact_phone' => $inputs['address_contact_phone'],
            'area_name'             => $areaInfo['full_name'],
            'address'               => $inputs['address'],
        ];
        $this->assertArraySubset($contents, $log->contents);
        $this->assertDatabaseHas('order_mainline_places', $contents);

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
