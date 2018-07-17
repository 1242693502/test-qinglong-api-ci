<?php

namespace Tests\Feature\InternalApi\Order\Log;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class CompleteLoadingLogTest extends TestCase
{
    use WithFaker;

    /**
     * 测试离开装货地
     *
     */
    public function testCreate()
    {
        $this->withoutMiddleware();

        $orderUUID = '5110693670404';

        $attributes = [
            'driver_uuid' => '2110692927600',
            'remark'      => $this->faker->sentence(),
            'longitude'   => $this->faker->longitude,
            'latitude'    => $this->faker->latitude,
            'has_invoice' => 0,
        ];

        $response = $this->postJson('/internal-api/mainline-orders/' . $orderUUID . '/complete-loading-logs', $attributes);

        $response->assertSuccessful();
        $response->assertJson(Arr::except($attributes, ['driver_uuid', 'has_invoice']));
    }

}
