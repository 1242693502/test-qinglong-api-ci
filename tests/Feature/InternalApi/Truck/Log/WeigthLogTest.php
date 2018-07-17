<?php

namespace Tests\Feature\InternalApi\Truck\Log;


use Illuminate\Support\Arr;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class WeigthLogTest extends TestCase
{
    use WithFaker;

    /**
     * 测试录过磅单
     */
    public function testCreate()
    {
        $driverUUID = '2110694686888';
        $attributes = [
            'driver_uuid' => $driverUUID,
            'goods_weight' => 60000,
            'total_price' => 30000,
            'images' => ['123', '234', '345'],
            'merchant_name' => $this->faker->company,
            'remark'        => $this->faker->sentence(),
            'longitude'     => $this->faker->longitude,
            'latitude'      => $this->faker->latitude,
            'has_invoice'   => 0,
        ];

        $response = $this->postJson('internal-api/truck-weight-logs', $attributes);

        $response->assertSuccessful();
        $response->assertJson(Arr::except($attributes, ['driver_uuid', 'goods_weight', 'has_invoice']));

    }

}