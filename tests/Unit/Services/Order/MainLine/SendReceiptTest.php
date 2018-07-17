<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Attribute;
use App\Models\Order\MainLine\Log;
use App\Services\Order\MainLine\Log\SendReceiptService;
use Tests\TestCase;

class SendReceiptTest extends TestCase
{
    /**
     * 测试记录交接单据 - 给
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
    public function testSendReceipt($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];

        $mainLineAttribute = Attribute::where('order_uuid', $orderUUID)
            ->whereNotNUll('receipt_images')
            ->whereNotNull('receipt_statuses')
            ->first(['id', 'receipt_images', 'receipt_statuses']);

        $inputs = [
            'remark'         => '测试记录交接单据 - 给',
            'receipt_images' => ['1111', '2222', '3333'],
        ];

        (new SendReceiptService())->create($orderUUID, $driverUUID, $inputs);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.send_receipt'),
            'title'       => cons()->lang('order.mainline.log.type.send_receipt'),
            'description' => '记录交接单据，共包含照片：3张',
            'remark'      => '测试记录交接单据 - 给',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.send_receipt'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '随车单据照片1', 'code' => 1111],
            ['name' => '随车单据照片2', 'code' => 2222],
            ['name' => '随车单据照片3', 'code' => 3333],
        ];

        $contents = [
            'receipt_statuses' => [1, 1, 1],
            'receipt_images'   => $mainLineAttribute->receipt_images,
        ];
        $this->assertArraySubset($images, $log->images);
        $this->assertArraySubset($contents, $log->contents);

        $orderMainlineAttribute = [
            'id'               => $mainLineAttribute->id,
            'receipt_statuses' => [1, 1, 1],
        ];
        $this->assertDatabaseHas('order_mainline_attribute', $orderMainlineAttribute);
    }
}
