<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Services\Order\MainLine\Log\RecordSealsService;
use Tests\TestCase;

class RecordSealsTest extends TestCase
{
    /**
     * 测试录封签号
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
    public function testRecordSeals($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];

        $inputs = [
            'remark'            => '测试录封签号',
            'seal_first_image'  => 1111,
            'seal_second_image' => 2222,
            'seal_last_image'   => 3333,
            'seal_first_no'     => '123456789',
            'seal_second_no'    => '123456789123',
            'seal_last_no'      => '123456789123456',
        ];

        (new RecordSealsService())->create($orderUUID, $driverUUID, $inputs);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.record_seals'),
            'title'       => cons()->lang('order.mainline.log.type.record_seals'),
            'description' => '录封签号，共包含照片：3张',
            'remark'      => '测试录封签号',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.record_seals'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '封签号边门1', 'code' => '1111'],
            ['name' => '封签号边门2', 'code' => '2222'],
            ['name' => '封签号尾门', 'code' => '3333'],
        ];
        $this->assertArraySubset($images, $log->images);

        $contents = [
            'seal_first_image'  => 1111,
            "seal_second_image" => 2222,
            'seal_last_image'   => 3333,
            'seal_first_no'     => '123456789',
            'seal_second_no'    => '123456789123',
            'seal_last_no'      => '123456789123456',
        ];

        $this->assertArraySubset($contents, $log->contents);

        $attribute = [
            'order_uuid'        => $orderUUID,
            'seal_first_no'     => $inputs['seal_first_no'],
            'seal_first_image'  => $inputs['seal_first_image'],
            'seal_second_no'    => $inputs['seal_second_no'],
            'seal_second_image' => $inputs['seal_second_image'],
            'seal_last_no'      => $inputs['seal_last_no'],
            'seal_last_image'   => $inputs['seal_last_image'],
        ];
        $this->assertDatabaseHas('order_mainline_attribute', $attribute);
    }
}
