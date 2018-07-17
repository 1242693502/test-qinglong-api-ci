<?php

namespace Tests\Unit\Services\Truck;

use App\Models\Order\OrderMainLine;
use Tests\TestCase;
use App\Services\Order\MainLine\TruckService;
use Urland\Exceptions\Client\BadRequestException;
use Urland\Exceptions\Client\ForbiddenException;
use Urland\Exceptions\Client\NotFoundException;
use Urland\Exceptions\Client\ValidationException;

class AppointTruckTest extends TestCase
{
    /**
     * 测试订单指派车辆失败
     *
     * @param $orderMainLine
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     *
     * @return bool
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testAppointTruckFail_01($orderMainLine)
    {
        $orderUUID = $orderMainLine->order_uuid;
        $truckUUID = '1111111111111';

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('参数验证失败');
        $result = (new TruckService())->appointTruck($orderUUID, $truckUUID);

        return true;
    }

    /**
     * 测试订单指派车辆失败
     *
     * @param $orderMainLine
     * @param $truck
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Truck\TruckTest::testCreate_2
     *
     * @return bool
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testAppointTruckFail_02($orderMainLine, $truck)
    {
        $orderUUID = $orderMainLine->order_uuid;
        $truckUUID = $truck->truck_uuid;

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('当前车辆状态不允许分配订单');
        $result = (new TruckService())->appointTruck($orderUUID, $truckUUID);

        return true;
    }

    /**
     * 测试订单指派车辆失败
     *
     * @param $orderMainLine
     * @param $truck
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Truck\TruckTest::testUpdate_1
     *
     * @return bool
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testAppointTruckFail_03($orderMainLine, $truck)
    {
        $orderUUID = $orderMainLine->order_uuid;
        $truckUUID = $truck->truck_uuid;

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('当前车辆没有主司机');
        $result = (new TruckService())->appointTruck($orderUUID, $truckUUID);

        return true;
    }

    /**
     * 测试订单指派车辆失败
     *
     * @param $truck
     *
     * @depends Tests\Unit\Services\Truck\TruckTest::testUpdate_1
     *
     * @return bool
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testAppointTruckFail_04($truck)
    {
        $orderUUID = '1111111111111';
        $truckUUID = $truck->truck_uuid;

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('订单不存在');
        $result = (new TruckService())->appointTruck($orderUUID, $truckUUID);

        return true;
    }

    /**
     * 测试订单指派车辆失败
     *
     * @param $orderMainLine
     * @param $truck
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate_1
     * @depends Tests\Unit\Services\Truck\TruckTest::testCreate_1
     *
     * @return bool
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testAppointTruckFail_05($orderMainLine, $truck)
    {
        $orderUUID = $orderMainLine->order_uuid;
        $truckUUID = $truck->truck_uuid;

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('订单未创建成功或订单已被指派');
        $result = (new TruckService())->appointTruck($orderUUID, $truckUUID);

        return true;
    }

    /**
     * 测试订单指派车辆
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
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */

        public function testAppointTruck($orderMainLine, $truck)
        {
            $orderUUID = $orderMainLine->order_uuid;
            $truckUUID = $truck->truck_uuid;

            $result = (new TruckService())->appointTruck($orderUUID, $truckUUID);
            $this->assertTrue($result instanceof OrderMainLine);

            $orderTruck = [
                'order_uuid'  => $orderUUID,
                'truck_uuid'  => $truckUUID,
                'truck_plate' => $truck->license_plate_number,
                'status'      => 1,
                'note'        => '派单',
            ];
            $this->assertDatabaseHas('order_mainline_truck', $orderTruck);

            $orderStatus = [
                'order_uuid'   => $orderUUID,
                'order_status' => cons('order.mainline.status.driver_confirm'),
            ];
            $this->assertDatabaseHas('order_mainline_statuses', $orderStatus);

            $orderLog = [
                'order_uuid'  => $orderUUID,
                'truck_uuid'  => $truckUUID,
                'type'        => cons('order.mainline.log.type.appoint_truck'),
                'description' => '订单指派车辆，车辆车牌：' . $truck->license_plate_number,
            ];
            $this->assertDatabaseHas('order_mainline_logs', $orderLog);

            $truckData = [
                'id'           => $truck->id,
                'truck_status' => cons('truck.status.driver_confirm'),
            ];
            $this->assertDatabaseHas('trucks', $truckData);

            $truckStatus = [
                'truck_uuid'   => $truck->truck_uuid,
                'truck_status' => cons('truck.status.driver_confirm'),
            ];
            $this->assertDatabaseHas('truck_statuses', $truckStatus);

            return $truck;
        }

}
