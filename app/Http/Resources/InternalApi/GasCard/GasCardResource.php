<?php

namespace App\Http\Resources\InternalApi\GasCard;

use App\Http\Resources\InternalApi\BaseResource;


class GasCardResource extends BaseResource
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
            'gc_card_no'    => $this['gc_card_no'],
            'gc_prov_name'  => $this['gc_prov_name'],
            'gc_city_name'  => $this['gc_city_name'],
            'gc_com_code'   => $this['gc_com_code'],
            'gc_com_name'   => cons()->valueLang('truck.gas_card.channel', $this['gc_com_code']),
            't_sid'         => $this['t_sid'],
            't_front_plate' => $this['t_front_plate'],
        ];
    }
}
