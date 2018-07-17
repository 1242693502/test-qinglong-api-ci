<?php

namespace Tests\Feature\InternalApi\Truck\Log;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class AdBlueLogTest extends TestCase
{
    use WithFaker;

    /**
     * 测试创建尿素记录
     *
     */
    public function testCreate()
    {
        $this->withoutMiddleware();

        $attributes = [
            'driver_uuid'  => '2110642191631',
            'liter_number' => $this->faker->randomDigit,
            'images'       => ['12345678', '321123123'],
            'remark'       => $this->faker->sentence(),
            'longitude'    => $this->faker->longitude,
            'latitude'     => $this->faker->latitude,
            'has_invoice'  => 0,
        ];

        $response = $this->postJson('/internal-api/truck-adblue-logs', $attributes);

        $response->assertSuccessful();
        $response->assertJson(Arr::except($attributes, ['driver_uuid', 'has_invoice']));
    }

}
