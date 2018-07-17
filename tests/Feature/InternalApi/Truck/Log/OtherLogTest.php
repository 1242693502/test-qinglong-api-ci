<?php

namespace Tests\Feature\InternalApi\Truck\Log;

use Illuminate\Support\Arr;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class OtherLogTest extends TestCase
{
    use WithFaker;

    /**
     * 测试车辆其他记录
     */
    public function testCreate()
    {

        $attributes = [
            'driver_uuid'   => '2110642191631',
            'name'          => '违章罚款',
            'total_price'   => 10000,
            'images'        => ['12345678', '321123123'],
            'remark'        => $this->faker->sentence(),
            'merchant_name' => $this->faker->company,
            'longitude'     => $this->faker->longitude,
            'latitude'      => $this->faker->latitude,
            'has_invoice'   => 0,
        ];

        $response = $this->postJson('internal-api/truck-other-logs', $attributes);

        $response->assertSuccessful();
        $response->assertJson(Arr::except($attributes, ['driver_uuid', 'has_invoice']));
    }
}
