<?php

namespace Tests\Unit\Services\Truck\Log;


use App\Models\Truck\Log\Repair;
use App\Models\Truck\TruckLog;
use App\Services\Truck\Log\RepairService;
use Tests\TestCase;

class RepairLogTest extends TestCase
{
    /**
     * 测试添加维修保养记录
     *
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function testRepairLog($truckDriverData)
    {
        $truckUUID  = $truckDriverData['truck_uuid'];
        $driverUUID = $truckDriverData['driver_uuid'][0];

        $attributes = [
            'repair_type'     => 'care',
            'name'            => '保养',
            'total_price'     => 10000,
            'images'          => [
                '123456789123456789a1234567891234',
                '123456789123456789b1234567891234',
            ],
            'merchant_name'   => '深圳市悠然居网络科技有限公司',
            'longitude'       => '143.10616',
            'latitude'        => '11.049976',
            'has_invoice'     => true,
            'current_mileage' => '1000',
            'remark'          => '测试添加维修保养记录',
        ];

        $repair = (new RepairService())->create($driverUUID, $attributes);

        $this->assertTrue($repair instanceof Repair);

        $repairData = [
            'truck_uuid'     => $truckUUID,
            'driver_uuid'    => $driverUUID,
            'name'           => $attributes['name'],
            'total_price'    => $attributes['total_price'],
            'images'         => implode(',', $attributes['images']),
            'merchant_name'  => $attributes['merchant_name'],
            'longitude'      => $attributes['longitude'],
            'latitude'       => $attributes['latitude'],
            'has_invoice'    => true,
            'remark'         => $attributes['remark'],
            'repair_type_id' => cons('truck.log.repair_type.' . $attributes['repair_type']),
        ];
        $this->assertDatabaseHas('truck_repair_logs', $repairData);

        $truckLogData = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'type'        => cons('truck.log.type.repair'),
            'title'       => cons()->lang('truck.log.type.repair'),
            'description' => '维修项目：' . $attributes['name'] . '，维修费用：' . $attributes['total_price'] / 100 . ' 元' . ' 共包含照片：' . count($attributes['images']) . '张',
            'remark'      => $attributes['remark'],
        ];
        $this->assertDatabaseHas('truck_logs', $truckLogData);

        $truckLog = TruckLog::where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('truck.log.type.repair'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '维修图片1', 'code' => $attributes['images'][0]],
            ['name' => '维修图片2', 'code' => $attributes['images'][1]],
        ];
        $this->assertArraySubset($images, $truckLog->images);

        $contents = [
            'name'           => $attributes['name'],
            'total_price'    => $attributes['total_price'],
            'images'         => $attributes['images'],
            'merchant_name'  => $attributes['merchant_name'],
            'has_invoice'    => $attributes['has_invoice'],
            'repair_type_id' => cons('truck.log.repair_type.' . $attributes['repair_type']),
        ];

        $this->assertArraySubset($contents, $truckLog->contents);

    }
}