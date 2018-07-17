<?php

namespace App\Http\Resources\InternalApi\Truck\Log;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class OtherResource
 *
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Truck\Log\Other
 */
class OtherResource extends BaseResource
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
            'truck_uuid'    => $this->truck_uuid,
            'name'          => $this->name,
            'total_price'   => $this->total_price,
            'images'        => $this->images,
            'merchant_name' => $this->merchant_name,
            'remark'        => $this->remark,
            'longitude'     => $this->longitude,
            'latitude'      => $this->latitude
        ];
    }
}
