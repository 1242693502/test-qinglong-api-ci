<?php

namespace Tests\Unit\Services\Truck\Log;


use App\Models\Truck\Log\Other;
use App\Models\Truck\TruckLog;
use App\Services\Truck\Log\WeightService;
use Tests\TestCase;

class WeigthLogTest extends TestCase
{
    /**
     * 测试录过磅单
     *
     * @param $truckDriverData
     *
     * @depends Tests\Unit\Services\Driver\AppointDriversTest::testAppointDrivers
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testWeigthLog($truckDriverData)
    {
        $truckUUID  = $truckDriverData['truck_uuid'];
        $driverUUID = $truckDriverData['driver_uuid'][0];

        $attributes = [
            'goods_weight'    => 60000,
            'total_price'     => 20000,
            'images'          => [
                '123456789123456789a1234567891234',
                '123456789123456789b1234567891234',
            ],
            'merchant_name'   => '深圳市悠然居网络科技有限公司',
            'longitude'       => '143.10616',
            'latitude'        => '11.049976',
            'has_invoice'     => true,
            'current_mileage' => '1000',
            'remark'          => '测试录过磅单',
        ];

        $other = (new WeightService())->create($driverUUID, $attributes);

        $this->assertTrue($other instanceof Other);

        $otherData = [
            'truck_uuid'    => $truckUUID,
            'driver_uuid'   => $driverUUID,
            'name'          => '录过磅单',
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
            'type'        => cons('truck.log.type.weight'),
            'title'       => cons()->lang('truck.log.type.weight'),
            'description' => '录过磅单，过磅重量：' . $attributes['goods_weight'] / 1000 . ' 吨 费用总价：' . $attributes['total_price'] / 100 . ' 元' . ' 共包含照片：' . count($attributes['images']) . '张',
            'remark'      => $attributes['remark'],
        ];
        $this->assertDatabaseHas('truck_logs', $truckLogData);

        $truckLog = TruckLog::where('truck_uuid', $truckUUID)
            ->where('driver_uuid', $driverUUID)
            ->where('type', cons('truck.log.type.weight'))
            ->orderByDesc('id')->first(['images', 'contents']);

        $images = [
            ['name' => '过磅单照片1', 'code' => $attributes['images'][0]],
            ['name' => '过磅单照片2', 'code' => $attributes['images'][1]],
        ];
        $this->assertArraySubset($images, $truckLog->images);

        $contents = [
            'goods_weight'  => $attributes['goods_weight'],
            'total_price'   => $attributes['total_price'],
            'images'        => $attributes['images'],
            'merchant_name' => $attributes['merchant_name'],
            'has_invoice'   => $attributes['has_invoice'],
        ];

        $this->assertArraySubset($contents, $truckLog->contents);

    }
}