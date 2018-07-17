<?php

namespace Tests\Unit\Services\Order;

use Tests\TestCase;

class StatusTest extends TestCase
{
    /**
     * 测试方法
     *
     * @param $orderUUID
     * @depends Tests\Unit\Services\Order\OrderTest::testCreate
     */
    public function testChangeStatus_1($orderUUID)
    {
        var_dump($orderUUID);
    }

}