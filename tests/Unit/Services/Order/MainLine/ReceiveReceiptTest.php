<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Models\Order\OrderMainLine;
use App\Services\Order\MainLine\Log\ReceiveReceiptService;
use Tests\TestCase;

class ReceiveReceiptTest extends TestCase
{
    /**
     * 测试交接单据
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
    public function testReceiveReceipt($orderMainLine, $truckDriverData)
    {
        $orderUUID     = $orderMainLine->order_uuid;
        $driverUUIDS   = $truckDriverData['driver_uuid'];
        $truckUUID     = $truckDriverData['truck_uuid'];
        $driverUUID    = $driverUUIDS[0];
        $orderMainLine = OrderMainLine::where('order_uuid', $orderUUID)->first(['contract_no']);

        $inputs = [
            'remark'         => '测试交接单据',
            'contract_no'    => $orderMainLine->contract_no,
            'receipt_images' => [1111, 2222, 3333],
            'contract_image' => 4444,
        ];

        (new ReceiveReceiptService())->create($orderUUID, $driverUUID, $inputs);

        $orderMainlineLog = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.receive_receipt'),
            'title'       => cons()->lang('order.mainline.log.type.receive_receipt'),
            'description' => '提交交接单据，合同编号：' . $orderMainLine->contract_no . ' 共包含照片：4张',
            'remark'      => '测试交接单据',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.receive_receipt'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '合同照片', 'code' => '4444'],
            ['name' => '随车单据照片1', 'code' => '1111'],
            ['name' => '随车单据照片2', 'code' => '2222'],
            ['name' => '随车单据照片3', 'code' => '3333'],
        ];
        $this->assertArraySubset($images, $log->images);

        $contents = [
            'contract_no'      => $orderMainLine->contract_no,
            "contract_image"   => 4444,
            'receipt_images'   => [1111, 2222, 3333],
            'receipt_statuses' => [0, 0, 0],
        ];
        $this->assertArraySubset($contents, $log->contents);

        $attribute = [
            'order_uuid'       => $orderUUID,
            'contract_no'      => $orderMainLine->contract_no,
            'contract_image'   => 4444,
            'receipt_images'   => '1111,2222,3333',
            'receipt_statuses' => '0,0,0',
        ];
        $this->assertDatabaseHas('order_mainline_attribute', $attribute);
    }
}
