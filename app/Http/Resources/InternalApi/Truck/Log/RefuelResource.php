<?php

namespace App\Http\Resources\InternalApi\Truck\Log;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class RefuelResource
 *
 * @package App\Http\Resources
 * @mixin \App\Models\Truck\Log\Refuel
 */
class RefuelResource extends BaseResource
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
            'truck_uuid'      => $this->truck_uuid,
            'per_price'       => $this->per_price,
            'liter_number'    => $this->liter_number,
            'total_price'     => $this->total_price,
            'pay_type_id'     => $this->pay_type_id,
            'gas_card_no'     => $this->gas_card_no,
            'current_mileage' => $this->current_mileage,
            'images'          => $this->images,
            'merchant_name'   => $this->merchant_name,
            'remark'          => $this->remark,
            'longitude'       => $this->longitude,
            'latitude'        => $this->latitude
        ];
    }
}
