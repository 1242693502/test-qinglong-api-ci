<?php

namespace Tests\Unit\Services\Driver;

use App\Models\Order\MainLine\Log;
use App\Models\Order\OrderMainLine;
use App\Services\Order\MainLine\LogService;
use Illuminate\Support\Arr;
use Tests\TestCase;

class LogTest extends TestCase
{
    /**
     * 测试生成快速访问文档
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testPrintQuickAccessDocs()
    {
        $logService = new LogService();

        $docs = $logService->printQuickAccessDocs();
        foreach ($docs as $line) {
            echo '* ' . $line . "\n";
        }

        $this->assertTrue(Arr::accessible($docs));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testCreateAppointTruck()
    {
        $logService = new LogService();

        $order    = OrderMainLine::first();
        $orderLog = $logService->logAppointTruck($order, $order->truck_uuid, [
            'license_plate_number' => '粤BAAAAA',
        ]);

        $this->assertTrue($orderLog instanceof Log);
    }
}