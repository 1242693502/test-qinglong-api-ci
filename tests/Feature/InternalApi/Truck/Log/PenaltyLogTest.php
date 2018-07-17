<?php

namespace Tests\Feature\InternalApi\Truck\Log;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class PenaltyLogTest extends TestCase
{
    use WithFaker;

    /**
     * 测试创建车辆罚款记录
     *
     */
    public function testCreate()
    {
        $this->withoutMiddleware();

        $attributes = [
            'driver_uuid'    => '2110691699537',
            'images'         => ['12345678', '321123123'],
            'penalty_points' => 10,
            'total_price'    => 100,
            'remark'         => $this->faker->sentence(),
            'longitude'      => $this->faker->longitude,
            'latitude'       => $this->faker->latitude,
            'has_invoice'    => 1,
            'penalty_date'   => '2018-05-23',
        ];

        $response = $this->postJson('/internal-api/truck-penalty-logs', $attributes);

        $response->assertSuccessful();
        $response->assertJson(Arr::except($attributes, ['driver_uuid', 'has_invoice']));
    }

}
