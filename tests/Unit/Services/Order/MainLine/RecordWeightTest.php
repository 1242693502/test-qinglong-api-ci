<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Models\Order\MainLine\Place;
use App\Models\Truck\Log\Other;
use App\Models\Truck\TruckLog;
use App\Services\Order\MainLine\Log\RecordWeightService;
use Tests\TestCase;

class RecordWeightTest extends TestCase
{
    /**
     * 测试录过磅单
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
    public function testRecordWeight($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];

        $inputs = [
            'remark'          => '测试录过磅单',
            'goods_weight'    => 100,
            'total_price'     => 10,
            'has_invoice'     => 1,
            'merchant_name'   => 321,
            'current_mileage' => 123,
            'longitude'       => 113.93041,
            'latitude'        => 22.53332,
            'images'          => [1111, 2222, 3333],
        ];

        (new RecordWeightService())->create($orderUUID, $driverUUID, $inputs);

        $orderMainLineData = [
            'order_uuid'   => $orderUUID,
            'truck_uuid'   => $truckUUID,
            'goods_weight' => $inputs['goods_weight'],
        ];
        $this->assertDatabaseHas('order_mainlines', $orderMainLineData);

        $orderMainLinePlace = Place::where('order_uuid', $orderUUID)
            ->where('type', cons('order.mainline.place.type.loading'))
            ->orderByDesc('arrival_time')->first();

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.record_weight'),
            'title'       => cons()->lang('order.mainline.log.type.record_weight'),
            'description' => '录过磅单: ' . $orderMainLinePlace->area_name . $orderMainLinePlace->address,
            'remark'      => '测试录过磅单',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.record_weight'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '过磅单照片1', 'code' => '1111'],
            ['name' => '过磅单照片2', 'code' => '2222'],
            ['name' => '过磅单照片3', 'code' => '3333'],
        ];
        $this->assertArraySubset($images, $log->images);

        $contents = [
            'place_uuid'    => $orderMainLinePlace->place_uuid,
            'full_address'  => $orderMainLinePlace->area_name . $orderMainLinePlace->address,
            'goods_weight'  => $inputs['goods_weight'],
            'total_price'   => $inputs['total_price'],
            'has_invoice'   => $inputs['has_invoice'],
            'merchant_name' => $inputs['merchant_name'],
            'images'        => $inputs['images'],
        ];
        $this->assertArraySubset($contents, $log->contents);

        $truckOtherLog = [
            'truck_uuid'    => $truckUUID,
            'driver_uuid'   => $driverUUID,
            'order_uuid'    => $orderUUID,
            'name'          => '录过磅单',
            'total_price'   => $inputs['total_price'],
            'has_invoice'   => $inputs['has_invoice'],
            'merchant_name' => $inputs['merchant_name'],
            'longitude'     => $inputs['longitude'],
            'latitude'      => $inputs['latitude'],
            'remark'        => $inputs['remark'],
        ];
        $this->assertDatabaseHas('truck_other_logs', $truckOtherLog);
        $other = Other::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->first(['images']);

        $images = [1111, 2222, 3333];
        $this->assertArraySubset($images, $other->images);

        $truckLog = [
            'truck_uuid'      => $truckUUID,
            'driver_uuid'     => $driverUUID,
            'order_uuid'      => $orderUUID,
            'type'            => cons('truck.log.type.weight'),
            'title'           => cons()->lang('truck.log.type.weight'),
            'description'     => '录过磅单: ' . $orderMainLinePlace->area_name . $orderMainLinePlace->address,
            'status'          => 1,
            'remark'          => $inputs['remark'],
            'current_mileage' => $inputs['current_mileage'],
            'longitude'       => $inputs['longitude'],
            'latitude'        => $inputs['latitude'],
        ];
        $this->assertDatabaseHas('truck_logs', $truckLog);

        $truckLog = TruckLog::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('truck.log.type.weight'))
            ->where('title', cons()->lang('truck.log.type.weight'))
            ->first(['images']);

        $images = [1111, 2222, 3333];
        $this->assertArraySubset($images, $truckLog->images);
    }
}
