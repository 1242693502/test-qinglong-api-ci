<?php

namespace Tests\Unit\Services\Truck\Log;


use App\Models\Truck\Log\Other;
use App\Models\Truck\TruckLog;
use App\Services\Truck\Log\OtherService;
use Tests\TestCase;

class OtherLogTest extends TestCase
{
    /**
     * 测试添加其他记录
     *
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function testOtherLog($truckDriverData)
    {
        $truckUUID  = $truckDriverData['truck_uuid'];
        $driverUUID = $truckDriverData['driver_uuid'][0];

        $attributes = [
            'name'            => '违章罚款',
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
            'remark'          => '测试添加其他记录',
        ];

        $other = (new OtherService())->create($driverUUID, $attributes);

        $this->assertTrue($other instanceof Other);

        $otherData = [
            'truck_uuid'    => $truckUUID,
            'driver_uuid'   => $driverUUID,
            'name'          => $attributes['name'],
            'total_price'   => $attributes['total_price'],
            'images'        => implode(',', $attributes['images']),
            'merchant_name' => $attributes['merchant_name'],
            'longitude'     => $attributes['longitude'],
            'latitude'      => $attributes['latitude'],
            'has_invoice'   => true,
            'remark'        => $attributes['remark'],
        ];
        $this->assertDatabaseHas('truck_other_logs', $otherData);

        $truckLogData = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'type'        => cons('truck.log.type.other'),
            'title'       => cons()->lang('truck.log.type.other'),
            'description' => '车辆录入其他费用，费用名称：' . $attributes['name'] . ' 录入：' . ($attributes['total_price'] / 100) . ' 元' . ' 共包含照片：' . count($attributes['images']) . '张',
            'remark'      => $attributes['remark'],
        ];
        $this->assertDatabaseHas('truck_logs', $truckLogData);

        $truckLog = TruckLog::where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('truck.log.type.other'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '录入其他费用照片1', 'code' => $attributes['images'][0]],
            ['name' => '录入其他费用照片2', 'code' => $attributes['images'][1]],
        ];
        $this->assertArraySubset($images, $truckLog->images);

        $contents = [
            'name'          => $attributes['name'],
            'total_price'   => $attributes['total_price'],
            'images'        => $attributes['images'],
            'merchant_name' => $attributes['merchant_name'],
            'has_invoice'   => $attributes['has_invoice'],
        ];

        $this->assertArraySubset($contents, $truckLog->contents);

    }
}