<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Models\Order\MainLine\Place;
use App\Services\Order\MainLine\Log\AddUnloadingService;
use QingLong\Platform\Area\Area;
use Tests\TestCase;

class AddUnloadingTest extends TestCase
{
    /**
     * 测试添加多点卸货地
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
    public function testAddUnloading($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];

        $orderMainLinePlace = Place::where('order_uuid', $orderUUID)
            ->where('type', cons('order.mainline.place.type.unloading'))
            ->orderByDesc('arrival_time')->first();

        $inputs = [
            'remark'                => '测试添加多点卸货地',
            'area_code'             => '110101001',
            'address'               => '测试地址',
            'address_contact_name'  => '王五',
            'address_contact_phone' => '13428281877',
            'images'                => [1111, 2222, 3333],
        ];

        (new AddUnloadingService())->create($orderUUID, $driverUUID, $inputs);

        $areaInfo = app(Area::class)->getFinalInfo($inputs['area_code']);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.add_unloading'),
            'title'       => cons()->lang('order.mainline.log.type.add_unloading'),
            'description' => '添加卸货地：' . $areaInfo['full_name'] . $inputs['address'],
            'remark'      => '测试添加多点卸货地',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.add_unloading'))
            ->first(['contents']);

        $contents = [
            'order_uuid'            => $orderUUID,
            'type'                  => cons('order.mainline.place.type.unloading'),
            'address_contact_name'  => $orderMainLinePlace->address_contact_name,
            'address_contact_phone' => $orderMainLinePlace->address_contact_phone,
            'area_code'             => $inputs['area_code'],
            'area_name'             => $areaInfo['full_name'],
            'address'               => $inputs['address'],
            'address_contact_name'  => $inputs['address_contact_name'],
            'address_contact_phone' => $inputs['address_contact_phone'],
        ];
        $this->assertArraySubset($contents, $log->contents);
        $this->assertDatabaseHas('order_mainline_places', $contents);

        $place = Place::where('order_uuid', $orderUUID)
            ->where('type', cons('order.mainline.place.type.unloading'))
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
