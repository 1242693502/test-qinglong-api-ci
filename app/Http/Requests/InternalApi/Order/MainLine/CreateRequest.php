<?php

namespace App\Http\Requests\InternalApi\Order\MainLine;

use App\Http\Requests\InternalApi\BaseRequest;

class CreateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'out_trade_no'               => 'bail|required|string|max:32',
            'contract_no'                => 'bail|required|string|max:32',
            'shipper_name'               => 'bail|required|string|max:32',
            'shipper_user_name'          => 'bail|required|string|max:32',
            'shipper_user_phone'         => 'bail|required|string|max:16',
            'origin_city_code'           => 'bail|required|string|max:10',
            'destination_city_code'      => 'bail|required|string|max:10',
            'transport_no'               => 'bail|required|string|max:32',
            'goods_name'                 => 'bail|required|string|max:32',
            'goods_weight_appointment'   => 'bail|required|ql_int|max:99999999',
            'goods_volume_appointment'   => 'bail|required|ql_int|max:99999999',
            'order_notes'                => 'bail|nullable|string|max:255',
            'departure_time_appointment' => 'bail|required|date_format:' . DATE_RFC3339,
            'truck_plate_appointment'    => 'bail|required|string|max:8',
            'trailer_plate_appointment'  => 'bail|required|string|max:8',
            // 'goods_weight'               => 'bail|nullable|ql_int|max:99999999',
            // 'goods_volume'               => 'bail|nullable|ql_int|max:99999999',
            // 'truck_plate'                => 'bail|nullable|string|max:16',
            // 'trailer_plate'              => 'bail|nullable|string|max:16',

            'places'                                   => 'bail|required|array',
            'places.loading'                           => 'bail|required|array',
            'places.loading.*.address_contact_name'    => 'bail|required|string|max:8',
            'places.loading.*.address_contact_phone'   => 'bail|required|string|max:16',
            // 'places.loading.*.area_code'               => 'bail|required|string|max:16',
            'places.loading.*.address'                 => 'bail|required|string|max:256',
            'places.unloading'                         => 'bail|required|array',
            'places.unloading.*.address_contact_name'  => 'bail|required|string|max:8',
            'places.unloading.*.address_contact_phone' => 'bail|required|string|max:16',
            // 'places.unloading.*.area_code'             => 'bail|required|string|max:16',
            'places.unloading.*.address'               => 'bail|required|string|max:256',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'out_trade_no'               => '外部系统订单号',
            'contract_no'                => '合同编号',
            'shipper_name'               => '发货方名称',
            'shipper_user_name'          => '发货方联系人姓名',
            'shipper_user_phone'         => '发货方联系人电话',
            'origin_city_code'           => '线路起点城市标识',
            'origin_city_name'           => '线路起点城市名称',
            'destination_city_code'      => '线路终点城市标识',
            'destination_city_name'      => '线路终点城市名称',
            'transport_no'               => '运输批次',
            'goods_name'                 => '货物名称',
            'goods_weight_appointment'   => '预约货物重量（KG）',
            'goods_volume_appointment'   => '预约货物体积（立方分米）',
            'order_notes'                => '订单留言-补充说明',
            'departure_time_appointment' => '预约发车时间',
            'truck_plate_appointment'    => '预约车辆车牌',
            'trailer_plate_appointment'  => '预约挂车车牌',
            // 'goods_weight'               => '实际货物重量（KG）',
            // 'goods_volume'               => '实际货物体积（立方分米）',
            // 'truck_plate'                => '货车车牌',
            // 'trailer_plate'              => '挂车车牌',

            'places'                                   => '装卸货地址列表',
            'places.loading'                           => '装货地列表',
            'places.loading.*.address_contact_name'    => '装货地联系人名称',
            'places.loading.*.address_contact_phone'   => '装货地联系人电话',
            'places.loading.*.area_code'               => '装货地地区标识',
            'places.loading.*.address'                 => '装货地地址',
            'places.unloading'                         => '卸货地列表',
            'places.unloading.*.address_contact_name'  => '卸货地联系人名称',
            'places.unloading.*.address_contact_phone' => '卸货地联系人电话',
            'places.unloading.*.area_code'             => '卸货地地区标识',
            'places.unloading.*.address'               => '卸货地地址',
        ];
    }
}
