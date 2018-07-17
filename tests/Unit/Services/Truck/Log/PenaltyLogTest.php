<?php

namespace Tests\Unit\Services\Truck\Log;


use App\Models\Driver\Driver;
use App\Models\Truck\Log\Penalty;
use App\Models\Truck\TruckLog;
use App\Services\Truck\Log\PenaltyService;
use Tests\TestCase;

class PenaltyLogTest extends TestCase
{
    /**
     * 测试添加车辆罚款记录
     *
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     */
    public function testPenaltyLog($truckDriverData)
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
            'remark'          => '测试添加车辆罚款记录',
            'penalty_date'    => date('Y-m-d'),
            'penalty_points'  => 12,
        ];

        $penalty = (new PenaltyService())->create($driverUUID, $attributes);

        $this->assertTrue($penalty instanceof Penalty);

        $penaltyData = [
            'truck_uuid'     => $truckUUID,
            'driver_uuid'    => $driverUUID,
            'total_price'    => $attributes['total_price'],
            'images'         => implode(',', $attributes['images']),
            'merchant_name'  => $attributes['merchant_name'],
            'longitude'      => $attributes['longitude'],
            'latitude'       => $attributes['latitude'],
            'has_invoice'    => true,
            'remark'         => $attributes['remark'],
            'penalty_date'   => $attributes['penalty_date'],
            'penalty_points' => $attributes['penalty_points'],
        ];
        $this->assertDatabaseHas('truck_penalty_logs', $penaltyData);

        $driver       = Driver::where('driver_uuid', $driverUUID)->first(['name']);
        $truckLogData = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'type'        => cons('truck.log.type.penalty'),
            'title'       => cons()->lang('truck.log.type.penalty'),
            'description' => '车辆录入罚款信息，违章司机：' . ($driver->name) . ' 共扣分数：' . $attributes['penalty_points'] . '分 罚款： ' . ($attributes['total_price'] / 100) . ' 元' . ' 共包含照片：' . count($attributes['images']) . '张 违章日期：' . $attributes['penalty_date'],
            'remark'      => $attributes['remark'],
        ];
        $this->assertDatabaseHas('truck_logs', $truckLogData);

        $truckLog = TruckLog::where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('truck.log.type.penalty'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '罚款图片1', 'code' => $attributes['images'][0]],
            ['name' => '罚款图片2', 'code' => $attributes['images'][1]],
        ];
        $this->assertArraySubset($images, $truckLog->images);

        $contents = [
            'total_price'   => $attributes['total_price'],
            'images'        => $attributes['images'],
            'merchant_name' => $attributes['merchant_name'],
            'has_invoice'   => $attributes['has_invoice'],
            'penalty_date'   => $attributes['penalty_date'],
            'penalty_points'   => $attributes['penalty_points'],
        ];

        $this->assertArraySubset($contents, $truckLog->contents);

    }
}