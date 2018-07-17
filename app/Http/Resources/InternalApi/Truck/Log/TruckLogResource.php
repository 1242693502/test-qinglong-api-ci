<?php

namespace App\Http\Resources\InternalApi\Truck\Log;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class AdBlueResource
 *
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Truck\Log\AdBlue
 */
class TruckLogResource extends BaseResource
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
            'truck_uuid'  => $this->truck_uuid,
            'driver_uuid' => $this->driver_uuid,
            'images'      => $this->images,
            'remark'      => $this->remark,
            'longitude'   => $this->longitude,
            'latitude'    => $this->latitude,
        ];
    }
}
