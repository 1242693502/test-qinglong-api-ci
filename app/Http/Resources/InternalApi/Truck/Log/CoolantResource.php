<?php

namespace App\Http\Resources\InternalApi\Truck\Log;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class CoolantResource
 *
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Truck\Log\Coolant
 */
class CoolantResource extends BaseResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'truck_uuid'   => $this->truck_uuid,
            'liter_number' => $this->liter_number,
            'total_price'  => $this->total_price,
            'images'       => $this->images,
            'remark'       => $this->remark,
            'longitude'    => $this->longitude,
            'latitude'     => $this->latitude,
        ];
    }
}
