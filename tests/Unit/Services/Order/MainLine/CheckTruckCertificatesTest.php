<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\OrderMainLine;
use App\Models\Truck\TruckApproval;
use App\Services\Order\MainLine\Log\CheckTruckService;
use Tests\TestCase;
use Urland\Exceptions\Client\BadRequestException;
use Urland\Exceptions\Client\ForbiddenException;

class CheckTruckCertificatesTest extends TestCase
{
    /**
     * 测试检查车辆证件失败
     *
     * @param $orderMainLine
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testCheckTruckCertificatesFail_01($orderMainLine)
    {
        $orderUUID  = $orderMainLine->order_uuid;
        $driverUUID = '1111111111111';
        $inputs     = [
            'remark' => '测试检查车辆证件',
            'codes'  => [30, 31, 60],
        ];

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('该司机无驾驶中的车辆');
        (new CheckTruckService())->checkTruckCertificates($orderUUID, $driverUUID, $inputs);

        return true;
    }

    /**
     * 测试检查车辆证件失败
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testCheckTruckCertificatesFail_02($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $driverUUID  = $driverUUIDS[0];
        $inputs      = [
            'remark' => '测试检查车辆证件',
            'codes'  => [30, 31, 60],
        ];

        $orderMainLine->setAttribute('truck_uuid', '')->save();

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('该车辆不能操作该订单');
        (new CheckTruckService())->checkTruckCertificates($orderUUID, $driverUUID, $inputs);

        return true;
    }

    /**
     * 测试检查车辆证件失败
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testCheckTruckCertificatesFail_03($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];
        $inputs      = [
            'remark' => '测试检查车辆证件',
            'codes'  => [29, 31, 60],
        ];

        $orderMainLine->setAttribute('truck_uuid', $truckUUID)->save();

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('检查证件编号不存在');
        (new CheckTruckService())->checkTruckCertificates($orderUUID, $driverUUID, $inputs);

        return true;
    }

    /**
     * 测试检查车辆证件
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testCheckTruckCertificates($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];
        $inputs      = [
            'remark' => '测试检查车辆证件',
            'codes'  => [30, 31, 60],
        ];
        $this->useFakeTruckLog();
        (new CheckTruckService())->checkTruckCertificates($orderUUID, $driverUUID, $inputs);

        $orderMainLine = OrderMainLine::where('order_uuid', $orderUUID)->where('truck_uuid', $truckUUID)->first();

        $truckApproval = [
            'truck_uuid'  => $orderMainLine->truck_uuid,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('truck.approval.type.truck_certificates'),
            'type_name'   => cons()->lang('truck.approval.type.truck_certificates'),
            'description' => '车辆证件检查：证件缺失',
            'images'      => '[]',
            'remark'      => '测试检查车辆证件',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('truck_approvals', $truckApproval);

        $contents = [
            'missing' => [
                ['code' => 30, 'name' => '车头行驶证'],
                ['code' => 31, 'name' => '车头营运证'],
                ['code' => 60, 'name' => '油卡（主卡）']
            ]
        ];

        $truckApproval = TruckApproval::where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('order_uuid', $orderUUID)
            ->first(['contents']);

        $this->assertArraySubset($contents, $truckApproval->contents);

        $truck = [
            'is_available' => '0',
        ];
        $this->assertDatabaseHas('trucks', $truck);

        $orderMainlineLog = [
            'truck_uuid'  => $orderMainLine->truck_uuid,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.check_truck_certs'),
            'title'       => cons()->lang('order.mainline.log.type.check_truck_certs'),
            'description' => '车辆证件检查：证件缺失',
            'images'      => '[]',
            'remark'      => '测试检查车辆证件',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);
    }
}
