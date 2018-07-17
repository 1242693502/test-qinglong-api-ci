<?php

namespace Tests\Unit\Services\Truck\Log;


use App\Models\Truck\Log\TollRoad;
use App\Models\Truck\TruckLog;
use App\Services\Truck\Log\TollRoadService;
use Tests\TestCase;

class TollRoadLogTest extends TestCase
{
    /**
     * 测试录入路桥费用
     *
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function testTollRoadLog($truckDriverData)
    {
        $truckUUID  = $truckDriverData['truck_uuid'];
        $driverUUID = $truckDriverData['driver_uuid'][0];

        $attributes = [
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
            'remark'          => '测试录入路桥费用',
        ];

        $tollRoad = (new TollRoadService())->create($driverUUID, $attributes);

        $this->assertTrue($tollRoad instanceof TollRoad);

        $tollRoadData = [
            'truck_uuid'    => $truckUUID,
            'driver_uuid'   => $driverUUID,
            'total_price'   => $attributes['total_price'],
            'images'        => implode(',', $attributes['images']),
            'merchant_name' => $attributes['merchant_name'],
            'longitude'     => $attributes['longitude'],
            'latitude'      => $attributes['latitude'],
            'has_invoice'   => true,
            'remark'        => $attributes['remark'],
        ];
        $this->assertDatabaseHas('truck_toll_road_logs', $tollRoadData);

        $truckLogData = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'type'        => cons('truck.log.type.toll_road'),
            'title'       => cons()->lang('truck.log.type.toll_road'),
            'description' => '车辆通行费：' . ($attributes['total_price'] / 100) . ' 元' . ' 共包含照片：' . count($attributes['images']) . '张',
            'remark'      => $attributes['remark'],
        ];
        $this->assertDatabaseHas('truck_logs', $truckLogData);

        $truckLog = TruckLog::where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('truck.log.type.toll_road'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '车辆通行费照片1', 'code' => $attributes['images'][0]],
            ['name' => '车辆通行费照片2', 'code' => $attributes['images'][1]],
        ];
        $this->assertArraySubset($images, $truckLog->images);

        $contents = [
            'total_price'   => $attributes['total_price'],
            'images'        => $attributes['images'],
            'merchant_name' => $attributes['merchant_name'],
            'has_invoice'   => $attributes['has_invoice'],
        ];

        $this->assertArraySubset($contents, $truckLog->contents);

    }
}