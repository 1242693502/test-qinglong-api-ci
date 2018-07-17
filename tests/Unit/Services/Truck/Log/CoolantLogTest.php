<?php

namespace Tests\Unit\Services\Truck\Log;


use App\Models\Truck\Log\Coolant;
use App\Models\Truck\TruckLog;
use App\Services\Truck\Log\CoolantService;
use Tests\TestCase;

class CoolantLogTest extends TestCase
{
    /**
     * 测试录入加水费用
     *
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function testCoolantLog($truckDriverData)
    {
        $truckUUID  = $truckDriverData['truck_uuid'];
        $driverUUID = $truckDriverData['driver_uuid'][0];

        $attributes = [
            'liter_number'    => 1000,
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
            'remark'          => '测试录入加水费用',
        ];

        $coolant = (new CoolantService())->create($driverUUID, $attributes);

        $this->assertTrue($coolant instanceof Coolant);

        $coolantData = [
            'truck_uuid'    => $truckUUID,
            'driver_uuid'   => $driverUUID,
            'liter_number'  => $attributes['liter_number'],
            'total_price'   => $attributes['total_price'],
            'images'        => implode(',', $attributes['images']),
            'merchant_name' => $attributes['merchant_name'],
            'longitude'     => $attributes['longitude'],
            'latitude'      => $attributes['latitude'],
            'has_invoice'   => true,
            'remark'        => $attributes['remark'],
        ];
        $this->assertDatabaseHas('truck_coolant_logs', $coolantData);

        $truckLogData = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'type'        => cons('truck.log.type.coolant'),
            'title'       => cons()->lang('truck.log.type.coolant'),
            'description' => '车辆录入加水费用，加水：' . ($attributes['liter_number'] / 1000) . ' L' . ' 录入：' . ($attributes['total_price'] / 100) . ' 元' . ' 共包含照片：' . count($attributes['images']) . '张',
            'remark'      => $attributes['remark'],
        ];
        $this->assertDatabaseHas('truck_logs', $truckLogData);

        $truckLog = TruckLog::where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('truck.log.type.coolant'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '加水费用照片1', 'code' => $attributes['images'][0]],
            ['name' => '加水费用照片2', 'code' => $attributes['images'][1]],
        ];
        $this->assertArraySubset($images, $truckLog->images);

        $contents = [
            'liter_number'  => $attributes['liter_number'],
            'total_price'   => $attributes['total_price'],
            'images'        => $attributes['images'],
            'merchant_name' => $attributes['merchant_name'],
            'has_invoice'   => $attributes['has_invoice'],
        ];

        $this->assertArraySubset($contents, $truckLog->contents);

    }
}