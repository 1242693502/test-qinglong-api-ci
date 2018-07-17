<?php

namespace Tests\Unit\Services\Order\MainLine;

use App\Services\Order\MainLine\DriverService;
use Tests\TestCase;

class DriverTest extends TestCase
{
    /**
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testDriverConfirm()
    {
        $orderUUID = '5110649073294';
        $driverUUID = '2110622754345';

        $result = (new DriverService())->driverConfirm($orderUUID, $driverUUID);

        dd($result);
    }
}
