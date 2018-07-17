<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Models\Order\OrderMainLine;
use App\Models\Trailer\Trailer;
use App\Models\Truck\Truck;
use App\Models\Truck\TruckApproval;
use App\Services\Order\MainLine\Log\CheckTrailerService;
use Tests\TestCase;

class CheckTrailerCertificatesTest extends TestCase
{
    /**
     * 测试检查挂车证件
     *
     * @param $orderMainLine
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testCheckTrailerCertificates($orderMainLine, $truckDriverData)
    {
        $orderUUID   = $orderMainLine->order_uuid;
        $driverUUIDS = $truckDriverData['driver_uuid'];
        $truckUUID   = $truckDriverData['truck_uuid'];
        $driverUUID  = $driverUUIDS[0];
        $trailer     = Trailer::OrderBy('id')->first(['trailer_uuid', 'license_plate_number']);

        $inputs = [
            'remark'        => '测试检查挂车证件',
            'trailer_plate' => $trailer->license_plate_number,
            'codes'         => [10, 20],
            'images'        => [1111, 2222, 3333],
        ];

        (new CheckTrailerService())->create($orderUUID, $driverUUID, $inputs);

        $orderMainLine = OrderMainLine::where('order_uuid', $orderUUID)->where('truck_uuid', $truckUUID)->first();

        $truckApproval = [
            'truck_uuid'  => $orderMainLine->truck_uuid,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('truck.approval.type.trailer_certificates'),
            'type_name'   => cons()->lang('truck.approval.type.trailer_certificates'),
            'description' => '挂车证件检查：证件缺失 共包含照片：3张 挂车车牌号：' . $inputs['trailer_plate'],
            'remark'      => '测试检查挂车证件',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('truck_approvals', $truckApproval);

        $images = [
            ['name' => '挂车证件照片1', 'code' => 1111],
            ['name' => '挂车证件照片2', 'code' => 2222],
            ['name' => '挂车证件照片3', 'code' => 3333],
        ];

        $contents = [
            'missing' => [
                ['code' => 10, 'name' => '挂车行驶证'],
                ['code' => 20, 'name' => '挂车营运证'],
            ]
        ];

        $truckApproval = TruckApproval::where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('order_uuid', $orderUUID)
            ->where('type', cons('truck.approval.type.trailer_certificates'))
            ->first(['images', 'contents']);

        $this->assertArraySubset($images, $truckApproval->images);
        $this->assertArraySubset($contents, $truckApproval->contents);

        $truck = Truck::where('truck_uuid', $orderMainLine->truck_uuid)->first();
        $truck = [
            'id'           => $truck->id,
            'is_available' => '0',
        ];
        $this->assertDatabaseHas('trucks', $truck);

        $orderMainLineUpdateData = [
            'trailer_uuid'  => $trailer->trailer_uuid,
            'trailer_plate' => $trailer->license_plate_number,
        ];
        $this->assertDatabaseHas('order_mainlines', $orderMainLineUpdateData);

        $trailerData = [
            'order_uuid'    => $orderUUID,
            'trailer_uuid'  => $trailer->trailer_uuid,
            'trailer_plate' => $trailer->license_plate_number,
            'status'        => 1,
            'note'          => '挂车证件检查',
        ];
        $this->assertDatabaseHas('order_mainline_trailer', $trailerData);

        $orderMainlineLog = [
            'truck_uuid'  => $orderMainLine->truck_uuid,
            'driver_uuid' => $driverUUID,
            'order_uuid'  => $orderUUID,
            'type'        => cons('order.mainline.log.type.check_trailer_certs'),
            'title'       => cons()->lang('order.mainline.log.type.check_trailer_certs'),
            'description' => '挂车证件检查：证件缺失 共包含照片：3张 挂车车牌号：' . $inputs['trailer_plate'],
            'remark'      => '测试检查挂车证件',
            'status'      => cons('truck.approval.status.waiting'),
        ];
        $this->assertDatabaseHas('order_mainline_logs', $orderMainlineLog);

        $log = Log::where('order_uuid', $orderUUID)
            ->where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('order.mainline.log.type.check_trailer_certs'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '挂车证件照片1', 'code' => 1111],
            ['name' => '挂车证件照片2', 'code' => 2222],
            ['name' => '挂车证件照片3', 'code' => 3333],
        ];
        $this->assertArraySubset($images, $log->images);

        $contents = [
            'trailer_plate' => $trailer->license_plate_number,
            'trailer_uuid'  => $trailer->trailer_uuid,
            'codes'         => [10, 20],
            'images'        => [1111, 2222, 3333],
            'names'         => ['挂车行驶证', '挂车营运证'],
        ];
        $this->assertArraySubset($contents, $log->contents);
    }
}
