<?php

namespace App\Services\Order;

use App\Models\Order\MainLine;
use App\Models\Order\OrderMainLine;
use App\Services\BaseService;
use App\Services\Order\MainLine\StatusService;
use Illuminate\Support\Facades\Validator;
use QingLong\Platform\Area\Area;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

/**
 * Class DriverService
 *
 * @package App\Services\Driver
 */
class OrderMainLineService extends BaseService
{
    /**
     * 创建一个订单
     *
     * @param $orderMainLineData
     *
     * @return \App\Models\Order\OrderMainLine
     * @throws \InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function create($orderMainLineData)
    {
        //TODO
        //涉及表包括
        //1. order_mainlines
        //2. order_mainline_places
        //3. order_mainline_statuses
        // 注意:
        // 1. 该接口不需要做http接口, 但需要做单元测试
        // 2. 该接口只录入货物相关内容，剔除司机和车辆相关数据
        // 业务流程
        // 1. 预留检查
        // 2. 创建 order_mainlines,状态为 0
        // 3. 写入 order_mainline_places 订单地址
        // 3. 修改订单状态为待调度(created),请参考 订单状态 常量(config/constant.php)


        // 参数验证
        $validator = Validator::make($orderMainLineData, [
            'out_trade_no'                             => 'required',
            'contract_no'                              => 'required',
            'shipper_name'                             => 'required',
            'shipper_user_name'                        => 'required',
            'shipper_user_phone'                       => 'required',
            'origin_city_code'                         => 'required',
            'origin_city_name'                         => '',
            'destination_city_code'                    => 'required',
            'destination_city_name'                    => '',
            'transport_no'                             => 'required',
            'goods_name'                               => 'required',
            'goods_weight_appointment'                 => 'required',
            'goods_volume_appointment'                 => 'required',
            'order_notes'                              => '',
            'departure_time_appointment'               => '',
            'truck_plate_appointment'                  => '',
            'trailer_plate_appointment'                => '',
            'places.loading.*.address_contact_name'    => 'required',
            'places.loading.*.address_contact_phone'   => 'required',
            'places.loading.*.area_code'               => 'required',
            'places.loading.*.address'                 => 'required',
            'places.unloading.*.address_contact_name'  => 'required',
            'places.unloading.*.address_contact_phone' => 'required',
            'places.unloading.*.area_code'             => 'required',
            'places.unloading.*.address'               => 'required',
        ]);

        if ($validator->fails()) {
            throw new Client\BadRequestException('您提交的参数有误，请重新提交');
        }

        //TODO contract_no 待处理

        // 获取订单，判断订单是否存在
        $oldOrderMainLine = OrderMainLine::where('out_trade_no',
            $orderMainLineData['out_trade_no'])->where('order_status', '!=',
            cons('order.mainline.status.uncreated'))->first();
        if ($oldOrderMainLine && $oldOrderMainLine->order_status !== cons('order.mainline.status.uncreated')) {
            // 订单已经完全创建完成
            throw Client\ValidationException::withMessages(['out_trade_no' => '订单已存在']);
        }

        // 创建新的订单
        // 获取装货地名称
        $areaInfo                              = app(Area::class)->getInfo($orderMainLineData['origin_city_code']);
        $orderMainLineData['origin_city_name'] = $areaInfo['full_name'];
        // 获取卸货地名称
        $areaInfo                                   = app(Area::class)->getInfo($orderMainLineData['destination_city_code']);
        $orderMainLineData['destination_city_name'] = $areaInfo['full_name'];

        $orderMainLine = OrderMainLine::create(array_except($orderMainLineData, ['places']));
        if (!$orderMainLine->exists) {
            throw new Server\InternalServerException('订单创建失败');
        }

        // 写入订单装卸货地点
        $orderMainLinePlaces = $this->createOrderMainLinePlaces($orderMainLine->order_uuid,
            $orderMainLineData['places']);

        // 订单状态改为1（created），写入订单状态表（order_mainline_status）
        (new StatusService())->changeStatus($orderMainLine, 'created');

        return $orderMainLine->fresh();

    }

    /**
     * 获取订单装卸货地址
     *
     * @param string $orderUUID
     * @param array  $places
     *
     * @return array
     */
    private function createOrderMainLinePlaces($orderUUID, $places)
    {
        $orderMainLinePlaces = [];

        foreach ($places as $key => $place) {
            if ($key == 'loading' || $key == 'unloading') {
                foreach ($place as $placeDetail) {
                    // 一条装卸货地数据
                    $orderMainLinePlaceData = [
                        'order_uuid'            => $orderUUID,
                        'address_contact_name'  => $placeDetail['address_contact_name'],
                        'address_contact_phone' => $placeDetail['address_contact_phone'],
                        'address'               => $placeDetail['address']
                    ];
                    // 装卸货 - 地址类型
                    $orderMainLinePlaceData['type'] = cons('order.mainline.place.type.' . $key);

                    $orderMainLinePlaces[] = MainLine\Place::create($orderMainLinePlaceData);
                }
            }
        }
        return $orderMainLinePlaces;
    }
}