<?php

namespace Tests\Unit\Services\Truck\Log;


use App\Models\GasCard\FillingStation;
use App\Models\Truck\Log\Refuel;
use App\Models\Truck\TruckLog;
use App\Services\Truck\Log\RefuelService;
use Tests\TestCase;

class RefuelLogTest extends TestCase
{
    /**
     * 测试添加成本-加油记录
     *
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public function testRefuelLog($truckDriverData)
    {
        $fillingStation = FillingStation::where('id', '1')->first();
        $truckUUID      = $truckDriverData['truck_uuid'];
        $driverUUID     = $truckDriverData['driver_uuid'][0];

        $attributes = [
            'total_price'        => 10000,
            'per_price'          => 100,
            'pay_type'           => 'fixed',
            'liter_number'       => 1000,
            'filling_station_id' => $fillingStation->id,
            'images'             => [
                '123456789123456789a1234567891234',
                '123456789123456789b1234567891234',
            ],
            'merchant_name'      => '深圳市悠然居网络科技有限公司',
            'longitude'          => '143.10616',
            'latitude'           => '11.049976',
            'has_invoice'        => true,
            'current_mileage'    => '1000',
            'remark'             => '测试添加成本-加油记录',
        ];

        $refuel = (new RefuelService())->create($driverUUID, $attributes);

        $this->assertTrue($refuel instanceof Refuel);

        $refuelData = [
            'truck_uuid'         => $truckUUID,
            'driver_uuid'        => $driverUUID,
            'total_price'        => $attributes['total_price'],
            'per_price'          => $attributes['per_price'],
            'liter_number'       => $attributes['liter_number'],
            'images'             => implode(',', $attributes['images']),
            'merchant_name'      => $attributes['merchant_name'],
            'longitude'          => $attributes['longitude'],
            'latitude'           => $attributes['latitude'],
            'has_invoice'        => true,
            'remark'             => $attributes['remark'],
            'pay_type_id'        => cons('truck.log.refuel_pay_type.' . $attributes['pay_type']),
        ];
        $this->assertDatabaseHas('truck_refuel_logs', $refuelData);

        $truckLogData = [
            'truck_uuid'  => $truckUUID,
            'driver_uuid' => $driverUUID,
            'type'        => cons('truck.log.type.refuel'),
            'title'       => cons()->lang('truck.log.type.refuel'),
            'description' => '加油站名称：' . $fillingStation->name . ' 定点加油：' . $attributes['liter_number'] / 1000 . 'L  ' . $attributes['total_price'] / 100 . '元 共包含照片：' . count($attributes['images']) . '张 当前里程：' . $attributes['current_mileage'] / 1000 . '公里',
            'remark'      => $attributes['remark'],
        ];
        $this->assertDatabaseHas('truck_logs', $truckLogData);

        $truckLog = TruckLog::where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('truck.log.type.refuel'))
            ->first(['images', 'contents']);

        $images = [
            ['name' => '录入加油费用照片1', 'code' => $attributes['images'][0]],
            ['name' => '录入加油费用照片2', 'code' => $attributes['images'][1]],
        ];
        $this->assertArraySubset($images, $truckLog->images);

        $contents = [
            'total_price'   => $attributes['total_price'],
            'images'        => $attributes['images'],
            'merchant_name' => $attributes['merchant_name'],
            'has_invoice'   => $attributes['has_invoice'],
            'pay_type_id'   => cons('truck.log.refuel_pay_type.' . $attributes['pay_type']),
            'gas_card_no'   => null,
        ];

        $this->assertArraySubset($contents, $truckLog->contents);

    }
}