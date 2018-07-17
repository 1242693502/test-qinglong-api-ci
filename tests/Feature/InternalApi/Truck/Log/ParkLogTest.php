<?php

namespace Tests\Feature\InternalApi\Truck\Log;

use Illuminate\Support\Arr;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class ParkLogTest extends TestCase
{
    use WithFaker;

    public function testCreate()
    {
        $this->withoutMiddleware();

        $attributes = [
            'driver_uuid'   => '2110642191631',
            'total_price'   => $this->faker->randomNumber(),
            'images'        => ['12345678', '321123123'],
            'merchant_name' => $this->faker->company,
            'remark'        => $this->faker->sentence(),
            'longitude'     => $this->faker->longitude,
            'latitude'      => $this->faker->latitude,
            'has_invoice'   => 0,

        ];

        $response = $this->postJson('/internal-api/truck-park-logs', $attributes);

        $response->assertSuccessful();
        $response->assertJson(Arr::except($attributes, ['driver_uuid', 'has_invoice']));
    }
}
