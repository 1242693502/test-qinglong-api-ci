<?php

namespace Tests\Feature\InternalApi\Order;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use WithFaker;

    protected $orderUUID          = '35110883085625';
    protected $driverUUID         = '2110694686888';
    protected $truckUUID          = '3110635011477';
    protected $loadingPlaceUUID   = '36110883086984';
    protected $unloadingPlaceUUID = '36110883086995';

    /**
     * 测试订单指派车辆
     */
    public function testAppointTruck()
    {
        $orderUUID = $this->orderUUID;
        $truckUUID = $this->truckUUID;

        $response = $this->patchJson('/internal-api/mainline-orders/' . $orderUUID . '/truck',
            ['truck_uuid' => $truckUUID]);

        $response->assertSuccessful();
    }

    /**
     * 测试司机确认接单
     */
    public function testDriverConfirm()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $uri        = '/internal-api/mainline-orders/' . $orderUUID . '/driver-confirm';

        $response = $this->patchJson($uri, ['driver_uuid' => $driverUUID]);

        $response->assertSuccessful();
    }

    /**
     * 测试检查车辆证件
     */
    public function testCheckTruckCertificates()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
            'codes'       => [],
        ];

        $uri = 'internal-api/mainline-orders/' . $orderUUID . '/check-truck-certificates';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试检查车辆
     */
    public function testCheckTruck()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
            'codes'       => [30],
            'remark'      => '发动机异常',
            'images'      => [1, 2, 3],
        ];

        $uri = 'internal-api/mainline-orders/' . $orderUUID . '/check-truck';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试记录进入高速
     */
    public function testCreateHighWayEnter()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
        ];

        $uri = 'internal-api/mainline-orders/' . $orderUUID . '/highway-enter-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试记录离开高速
     */
    public function testCreateHighWayLeave()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
        ];

        $uri = 'internal-api/mainline-orders/' . $orderUUID . '/highway-leave-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试记录堵车
     */
    public function testCreateTrafficJam()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
            'images'      => ['http://imageTest01.com/', 'http://imageTest02.com/'],
        ];

        $uri = 'internal-api/mainline-orders/' . $orderUUID . '/traffic-jam-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试到达装货地
     */
    public function testArriveLoading()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
            'place_uuid'  => $this->loadingPlaceUUID,
            'images'      => [],
        ];

        $uri = 'internal-api/mainline-orders/' . $orderUUID . '/arrive-loading-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试挂车证件检查
     */
    public function testCheckTrailerCerts()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid'   => $driverUUID,
            'trailer_plate' => '粤B1234挂',
            'codes'         => [],
            'images'        => []
        ];

        $uri = 'internal-api/mainline-orders/' . $orderUUID . '/check-trailer-certificates';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试挂车检查
     */
    public function testCheckTrailer()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
            'codes'       => [],
            'images'      => []
        ];

        $uri = 'internal-api/mainline-orders/' . $orderUUID . '/check-trailer';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试录封签号
     */
    public function testCreateRecordSeals()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid'       => $driverUUID,
            'seal_first_no'     => '111111',
            'seal_first_image'  => '12345678901234567890123456789012',
            'seal_second_no'    => '333333',
            'seal_second_image' => '12345678901234567890123456789012',
            'seal_last_no'      => '555555',
            'seal_last_image'   => '12345678901234567890123456789012',
        ];

        $uri = '/internal-api/mainline-orders/' . $orderUUID . '/record-seals-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试交接单据 - 收
     */
    public function testReceiveReceipt()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid'    => $driverUUID,
            'contract_no'    => '111222',
            'contract_image' => '222333',
            'receipt_images' => ['333444', '444555'],
        ];

        $uri = '/internal-api/mainline-orders/' . $orderUUID . '/receive-receipt-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试装货计时开始
     */
    public function testCountLoadingBegin()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
        ];

        $uri = '/internal-api/mainline-orders/' . $orderUUID . '/count-loading-begin-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试装货计时结束
     */
    public function testCountLoadingEnd()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
        ];

        $uri = '/internal-api/mainline-orders/' . $orderUUID . '/count-loading-end-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 无需计时
     */
    public function testNoCountLoading()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
        ];

        $uri = '/internal-api/mainline-orders/' . $orderUUID . '/no-count-loading-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试添加装货地
     */
    public function testAddLoading()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
            'area_code'   => '120102003',
            'address'     => '不知道',
        ];

        $uri = 'internal-api/mainline-orders/' . $orderUUID . '/add-loading-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试完成装货
     */
    public function testCompleteLoading()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
        ];

        $uri = 'internal-api/mainline-orders/' . $orderUUID . '/complete-loading-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试到达卸货地
     */
    public function testArriveUnloading()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
            'place_uuid'  => $this->unloadingPlaceUUID,
            'images'      => [],
        ];

        $uri = 'internal-api/mainline-orders/' . $orderUUID . '/arrive-unloading-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试交接单据 - 给
     */
    public function testSendReceipt()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid'    => $driverUUID,
            'receipt_images' => ['333444', '444555'],
        ];

        $uri = '/internal-api/mainline-orders/' . $orderUUID . '/send-receipt-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();

    }

    /**
     * 测试卸货计时开始
     */
    public function testCountUnloadingBegin()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
        ];

        $uri = '/internal-api/mainline-orders/' . $orderUUID . '/count-unloading-begin-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试卸货计时结束
     */
    public function testCountUnloadingEnd()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
        ];

        $uri = '/internal-api/mainline-orders/' . $orderUUID . '/count-unloading-end-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 无需计时
     */
    public function testNoCountUnloading()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
        ];

        $uri = '/internal-api/mainline-orders/' . $orderUUID . '/no-count-unloading-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试卸货异常
     */
    public function testCreateUnloadingAbnormal()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
            'description' => '货物跑了',
            'images'      => ['123', '234', '345'],
        ];

        $uri = 'internal-api/mainline-orders/' . $orderUUID . '/unloading-abnormal-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试添加卸货地
     */
    public function testAddUnloading()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
            'area_code'   => '120102003',
            'address'     => '不知道',
        ];

        $uri = 'internal-api/mainline-orders/' . $orderUUID . '/add-unloading-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试完成卸货
     */
    public function testCompleteUnloading()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
        ];

        $uri = 'internal-api/mainline-orders/' . $orderUUID . '/complete-unloading-logs';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

    /**
     * 测试运输完成
     */
    public function testComplete()
    {
        $orderUUID  = $this->orderUUID;
        $driverUUID = $this->driverUUID;
        $attributes = [
            'driver_uuid' => $driverUUID,
        ];

        $uri = 'internal-api/mainline-orders/' . $orderUUID . '/complete';

        $response = $this->postJson($uri, $attributes);

        $response->assertSuccessful();
    }

}
