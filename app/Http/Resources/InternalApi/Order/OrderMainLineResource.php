<?php

namespace App\Http\Resources\InternalApi\Order;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class OrderMainLineResource
 *
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Order\OrderMainLine
 */
class OrderMainLineResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'order_uuid'                 => $this->order_uuid,
            'out_trade_no'               => $this->out_trade_no,
            'contract_no'                => $this->contract_no,
            'shipper_name'               => $this->shipper_name,
            'shipper_user_name'          => $this->shipper_user_name,
            'shipper_user_phone'         => $this->shipper_user_phone,
            'origin_city_code'           => $this->origin_city_code,
            'destination_city_code'      => $this->destination_city_code,
            'transport_no'               => $this->transport_no,
            'goods_name'                 => $this->goods_name,
            'goods_weight_appointment'   => $this->goods_weight_appointment,
            'goods_volume_appointment'   => $this->goods_volume_appointment,
            'order_notes'                => $this->order_notes,
            'order_time'                 => $this->order_time,
            'departure_time_appointment' => $this->departure_time_appointment,
            'truck_plate_appointment'    => $this->truck_plate_appointment,
            'trailer_plate_appointment'  => $this->trailer_plate_appointment,
            'order_status'               => $this->order_status,
            'goods_weight'               => $this->goods_weight,
            'goods_volume'               => $this->goods_volume,
            'truck_uuid'                 => $this->truck_uuid,
            'trailer_uuid'               => $this->trailer_uuid,
            'truck_plate'                => $this->truck_plate,
            'trailer_plate'              => $this->trailer_uuid,
        ];
    }
}
