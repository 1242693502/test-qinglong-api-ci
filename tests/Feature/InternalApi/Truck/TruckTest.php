<?php

namespace Tests\Feature\InternalApi\Truck;

use Tests\TestCase;

class TruckTest extends TestCase
{
    /**
     * 测试司机绑定车辆
     */
    public function testAppointDrivers()
    {

        $truckUUID  = '3110623652298';
        $attributes = [
            'driverUUIDs' => [
                '2110622754345',
                '2110623266310'
            ],
            'remark' => '测试数据',
        ];

        $uri = '/internal-api/trucks/' . $truckUUID . '/drivers';

        $response = $this->patchJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     *  测试清空司机绑定车辆记录
     */
    public function testRemoveDrivers()
    {
        $truckUUID = '3110623652298';
        $uri = '/internal-api/trucks/' . $truckUUID . '/drivers';

        $response = $this->deleteJson($uri);

        $response->assertSuccessful();
    }
}
