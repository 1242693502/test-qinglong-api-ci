<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Models\Order\MainLine\Place;
use App\Models\Order\OrderMainLine;
use App\Services\Order\MainLine\Log\CompleteService;
use Tests\TestCase;

class CompleteTest extends TestCase
{
    /**
     * 测试运输完成
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
    public function testComplete($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];

        $inputs = [
            'remark' => '测试运输完成',
            'images' => [1111, 2222, 3333],
        ];

        (new CompleteService())->create($orderUUID, $driverUUID, $inputs);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.success'),
            'title'       => cons()->lang('order.mainline.log.type.success'),
            'description' => '记录运输完成',
            'remark'      => '测试运输完成',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.success'))
            ->first(['contents']);

        $contents = [
            'images' => $inputs['images'],
        ];
        $this->assertArraySubset($contents, $log->contents);

        $orderMainLine = OrderMainLine::where('order_uuid', $orderUUID)->where('truck_uuid', $truckUUID)->first();

        $orderMainLineStatus = [
            'order_uuid'   => $orderMainLine->order_uuid,
            'order_status' => cons('order.mainline.status.success'),
        ];
        $this->assertDatabaseHas('order_mainline_statuses', $orderMainLineStatus);

        $orderMainlineDriver = [
            'order_uuid' => $orderMainLine->order_uuid,
            'status'     => cons('order.mainline.driver.status.success'),
        ];
        $this->assertDatabaseHas('order_mainline_driver', $orderMainlineDriver);

        $truck = $orderMainLine->orderTruck;

        $truckStatuses = [
            'truck_uuid'   => $truck->truck_uuid,
            'truck_status' => cons('truck.status.available'),
        ];
        $this->assertDatabaseHas('truck_statuses', $truckStatuses);

        $truckData = [
            'id'           => $truck->id,
            'truck_status' => cons('truck.status.available'),
        ];
        $this->assertDatabaseHas('trucks', $truckData);
    }
}
