<?php

namespace Tests\Unit\Services\Truck\Log;


use App\Models\Truck\Log\AdBlue;
use App\Models\Truck\TruckLog;
use App\Services\Truck\Log\AdBlueService;
use Tests\TestCase;

class AdBlueLogTest extends TestCase
{
    /**
     * 测试添加尿素记录
     *
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function testAdBlueLog($truckDriverData)
    {
        $truckUUID  = $truckDriverData['truck_uuid'];
        $driverUUID = $truckDriverData['driver_uuid'][0];

        $attributes = [
            'has_invoice'     => true,
            'liter_number'    => '100',
            'current_mileage' => '1000',
            'images'          => [
                '123456789123456789a1234567891234',
                '123456789123456789b1234567891234',
            ],
            'longitude'       => '143.10616',
            'latitude'        => '11.049976',
            'remark'          => '测试添加尿素记录',
        ];

        $adBlue = (new AdBlueService())->create($driverUUID, $attributes);

        $this->assertTrue($adBlue instanceof AdBlue);

        $adBlueData = [
            'truck_uuid'   => $truckUUID,
            'driver_uuid'  => $driverUUID,
            'has_invoice'  => true,
            'liter_number' => $attributes['liter_number'],
            'images'       => implode(',', $attributes['images']),
            'longitude'    => $attributes['longitude'],
            'latitude'     => $attributes['latitude'],
            'remark'       => $attributes['remark'],
        ];
        $this->assertDatabaseHas('truck_adblue_logs', $adBlueData);

        $truckLogData = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'type'        => cons('truck.log.type.adblue'),
            'title'       => cons()->lang('truck.log.type.adblue'),
            'description' => '车辆领用尿素，领用：' . ($attributes['liter_number'] / 1000) . ' L' . ' 共包含照片：' . count($attributes['images']) . '张',
            'remark'      => $attributes['remark'],
        ];
        $this->assertDatabaseHas('truck_logs', $truckLogData);

        $truckLog = TruckLog::where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('truck.log.type.adblue'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '领用尿素照片1', 'code' => $attributes['images'][0]],
            ['name' => '领用尿素照片2', 'code' => $attributes['images'][1]],
        ];
        $this->assertArraySubset($images, $truckLog->images);

        $contents = [
            'has_invoice'  => $attributes['has_invoice'],
            'liter_number' => $attributes['liter_number'],
            'images'       => $attributes['images'],
        ];

        $this->assertArraySubset($contents, $truckLog->contents);

    }
}