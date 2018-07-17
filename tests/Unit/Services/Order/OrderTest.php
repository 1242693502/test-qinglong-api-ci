<?php

namespace Tests\Unit\Services\Order;

use App\Models\Order\OrderMainLine;
use App\Services\Order\OrderMainLineService;
use Tests\TestCase;
use Urland\Exceptions\Client\ValidationException;

class OrderTest extends TestCase
{

    /**
     * 测试创建一个订单
     *
     * @return \App\Models\Order\OrderMainLine
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testCreate()
    {
        $outTradeNo = '333222111555667791';
        $contractNo = '1234567890';

        $orderMainLineData = [
            'out_trade_no'               => $outTradeNo,
            'contract_no'                => $contractNo,
            'shipper_name'               => '张三店铺',
            'shipper_user_name'          => '张三',
            'shipper_user_phone'         => '18888888888',
            'origin_city_code'           => '110100',
            'destination_city_code'      => '120000',
            'transport_no'               => '555',
            'goods_name'                 => '肉干',
            'goods_weight_appointment'   => 100000,
            'goods_volume_appointment'   => 5000,
            'order_notes'                => '加急',
            'departure_time_appointment' => '2018-05-11 20:12:43',
            'truck_plate_appointment'    => '粤B12345',
            'trailer_plate_appointment'  => '粤B1234挂',
            'places'                     => [
                'loading'   => [
                    [
                        'address_contact_name'  => '张三',
                        'address_contact_phone' => '13866666666',
                        'area_code'             => '110101001',
                        'address'               => '北京天安门xxx街道几号'
                    ]
                ],
                'unloading' => [
                    [
                        'address_contact_name'  => '王五',
                        'address_contact_phone' => '13566666666',
                        'area_code'             => '110101001',
                        'address'               => '北京天安门xxx街道几号'
                    ]
                ],
            ]
        ];

        $orderMainLine = (new OrderMainLineService())->create($orderMainLineData);

        $this->assertTrue($orderMainLine instanceof OrderMainLine);

        $this->assertSame($outTradeNo, $orderMainLine->out_trade_no);
        $this->assertSame($contractNo, $orderMainLine->contract_no);

        return $orderMainLine;
    }

    /**
     * 测试创建一个订单
     *
     * @return \App\Models\Order\OrderMainLine
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testCreate_1()
    {
        $outTradeNo = '333222111555667792';
        $contractNo = '1234567891';

        $orderMainLineData = [
            'out_trade_no'               => $outTradeNo,
            'contract_no'                => $contractNo,
            'shipper_name'               => '李四店铺',
            'shipper_user_name'          => '李四',
            'shipper_user_phone'         => '18888888888',
            'origin_city_code'           => '110100',
            'destination_city_code'      => '120000',
            'transport_no'               => '555',
            'goods_name'                 => '肉干',
            'goods_weight_appointment'   => 100000,
            'goods_volume_appointment'   => 5000,
            'order_notes'                => '加急',
            'departure_time_appointment' => '2018-05-11 20:12:43',
            'truck_plate_appointment'    => '粤B12345',
            'trailer_plate_appointment'  => '粤B1234挂',
            'order_status'               => 88,
            'places'                     => [
                'loading'   => [
                    [
                        'address_contact_name'  => '张三',
                        'address_contact_phone' => '13866666666',
                        'area_code'             => '110101001',
                        'address'               => '北京天安门xxx街道几号'
                    ]
                ],
                'unloading' => [
                    [
                        'address_contact_name'  => '王五',
                        'address_contact_phone' => '13566666666',
                        'area_code'             => '110101001',
                        'address'               => '北京天安门xxx街道几号'
                    ]
                ],
            ]
        ];

        $orderMainLine = (new OrderMainLineService())->create($orderMainLineData);

        $this->assertTrue($orderMainLine instanceof OrderMainLine);

        $this->assertSame($outTradeNo, $orderMainLine->out_trade_no);
        $this->assertSame($contractNo, $orderMainLine->contract_no);

        $orderMainLine->setAttribute('order_status', $orderMainLineData['order_status'])->save();

        return $orderMainLine;
    }

    /**
     * 测试创建一个订单失败
     *
     * @return bool
     * @throws \InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testCreateFail()
    {
        $outTradeNo = '333222111555667791';
        $contractNo = '1234567890';

        $orderMainLineData = [
            'out_trade_no'               => $outTradeNo,
            'contract_no'                => $contractNo,
            'shipper_name'               => '张三店铺',
            'shipper_user_name'          => '张三',
            'shipper_user_phone'         => '18888888888',
            'origin_city_code'           => '110100',
            'destination_city_code'      => '120000',
            'transport_no'               => '555',
            'goods_name'                 => '肉干',
            'goods_weight_appointment'   => 100000,
            'goods_volume_appointment'   => 5000,
            'order_notes'                => '加急',
            'departure_time_appointment' => '2018-05-11 20:12:43',
            'truck_plate_appointment'    => '粤B12345',
            'trailer_plate_appointment'  => '粤B1234挂',
            'places'                     => [
                'loading'   => [
                    [
                        'address_contact_name'  => '张三',
                        'address_contact_phone' => '13866666666',
                        'area_code'             => '110101001',
                        'address'               => '北京天安门xxx街道几号'
                    ]
                ],
                'unloading' => [
                    [
                        'address_contact_name'  => '王五',
                        'address_contact_phone' => '13566666666',
                        'area_code'             => '110101001',
                        'address'               => '北京天安门xxx街道几号'
                    ]
                ],
            ]
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('参数验证失败');
        $result = (new OrderMainLineService())->create($orderMainLineData);

        return true;
    }

}
