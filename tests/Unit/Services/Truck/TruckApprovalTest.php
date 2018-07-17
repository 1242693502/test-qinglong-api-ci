<?php

namespace Tests\Unit\Services\Truck;

use App\Models\Truck\TruckApproval;
use App\Services\Truck\TruckApprovalService;
use Tests\TestCase;
use Urland\Exceptions\Client\BadRequestException;
use Urland\Exceptions\Client\ForbiddenException;

class TruckApprovalTest extends TestCase
{
    /**
     * 测试审核订单车辆检查失败
     *
     * @return bool
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     */
    public function testTruckApprovalFail_01()
    {
        $inputs = [
            'status'          => '88',
            'approver_uuid'   => '123456789',
            'approver_name'   => '测试',
            'approver_reason' => '测试审核通过',
        ];

        $truckApproval = TruckApproval::where('id', 1)->first(['id']);
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('审核状态错误');
        $result = (new TruckApprovalService())->approval($truckApproval->id, $inputs);

        return true;
    }

    /**
     * 测试审核订单车辆检查失败
     *
     * @return bool
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     */
    public function testTruckApprovalFail_02()
    {
        $inputs = [
            'status'          => '99',
            'approver_uuid'   => '123456789',
            'approver_name'   => '测试',
            'approver_reason' => '测试审核通过',
        ];

        $truckApproval = TruckApproval::where('id', 1)->first(['id']);
        $truckApproval->setAttribute('status', false)->save();
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('当前订单审核状态异常');
        $result = (new TruckApprovalService())->approval($truckApproval->id, $inputs);

        return true;
    }

    /**
     * 测试审核订单车辆检查
     *
     * @param $orderMainLine
     * @param $truck
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Truck\TruckTest::testCreate_1
     *
     * @return mixed
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     */
    public function testTruckApproval($orderMainLine, $truck)
    {
        $orderUUID = $orderMainLine->order_uuid;
        $truckUUID = $truck->truck_uuid;
        $inputs    = [
            'status'          => '99',
            'approver_uuid'   => '123456789',
            'approver_name'   => '测试',
            'approver_reason' => '测试审核通过',
        ];

        $truckApproval = TruckApproval::where('id', 1)->first(['id']);
        $truckApproval->setAttribute('status', true)->save();

        $result = (new TruckApprovalService())->approval($truckApproval->id, $inputs);

        $this->assertTrue($result instanceof TruckApproval);

        $truckApproval = [
            'order_uuid'      => $orderUUID,
            'status'          => $inputs['status'],
            'approver_uuid'   => $inputs['approver_uuid'],
            'approver_name'   => $inputs['approver_name'],
            'approver_reason' => $inputs['approver_reason'],
        ];
        $this->assertDatabaseHas('truck_approvals', $truckApproval);

        $truck = [
            'truck_uuid'   => $truckUUID,
            'is_available' => '1',
        ];
        $this->assertDatabaseHas('trucks', $truck);
    }

}
