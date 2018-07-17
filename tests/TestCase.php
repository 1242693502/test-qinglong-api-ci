<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use QingLong\Platform\TruckLog\TruckLog;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * 模拟行车日志接口
     *
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    protected function useFakeTruckLog()
    {
        $mock = $this->getMockBuilder(TruckLog::class)->getMock();

        $mock->expects($this->any())->method('getMileage')->willReturnCallback(function ()
        {
            return rand(100000000, 999999999);
        });

        $this->app->instance(TruckLog::class, $mock);
    }

}
