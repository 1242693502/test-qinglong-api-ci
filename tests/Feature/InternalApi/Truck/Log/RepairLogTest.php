<?php

namespace Tests\Feature\InternalApi\Truck\Log;

use Illuminate\Support\Arr;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class RepairLogTest extends TestCase
{
    use WithFaker;

    /**
     * 测试车辆维修保养记录
     */
    public function testCreate()
    {
        $this->withoutMiddleware();
        $attributes = [
            'driver_uuid'   => '2110691699537',
            'repair_type'   => 'care',
            'name'          => '保养',
            'total_price'   => 30000,
            'images'        => ['12345678', '321123123'],
            'merchant_name' => $this->faker->company,
            'remark'        => $this->faker->sentence(),
            'longitude'     => $this->faker->longitude,
            'latitude'      => $this->faker->latitude,
            'has_invoice'   => 0,
        ];

        $response = $this->postJson('internal-api/truck-repair-logs', $attributes);

        $response->assertSuccessful();
        $response->assertJson(Arr::except($attributes, ['driver_uuid', 'repair_type', 'has_invoice']));
    }
}
