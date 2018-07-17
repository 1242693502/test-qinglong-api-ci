<?php

namespace Tests\Feature\InternalApi\Truck\Log;

use Illuminate\Support\Arr;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class RefuelLogTest extends TestCase
{
    use WithFaker;

    /**
     * 测试车辆成本-加油记录
     */
    public function testCreate()
    {
        $this->withoutMiddleware();

        $perPrice    = $this->faker->numberBetween(10000, 99999);
        $literNumber = $this->faker->numberBetween(10000, 99999);

        $attributes = [
            'driver_uuid'     => '2110694686888',
            'per_price'       => $perPrice,
            'liter_number'    => $literNumber,
            'pay_type'        => 'fixed',
            'gas_card_no'     => '12345698754',
            'current_mileage' => 10000,
            'images'          => ['12345678', '321123123'],
            'merchant_name'   => $this->faker->company,
            'remark'          => $this->faker->sentence,
            'longitude'       => $this->faker->longitude,
            'latitude'        => $this->faker->latitude,
            'has_invoice'     => 0,

        ];

        $response = $this->postJson('internal-api/truck-refuel-logs', $attributes);

        $response->assertSuccessful();
        $response->assertJson(Arr::except($attributes, ['driver_uuid', 'pay_type', 'has_invoice']));
    }
}
