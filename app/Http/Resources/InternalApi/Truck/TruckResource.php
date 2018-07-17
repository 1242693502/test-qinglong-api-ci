<?php

namespace App\Http\Resources\InternalApi\Truck;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class TruckResource
 *
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Truck\Truck
 */
class TruckResource extends BaseResource
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
            'truck_uuid'           => $this->truck_uuid,
            'license_plate_number' => $this->license_plate_number,
            'belong_type'          => $this->belong_type,
            'brand'                => $this->brand,
            'engine_number'        => $this->engine_number,
            'axle_number'          => $this->axle_number,
            'type_code'            => $this->type_code,
            'type_name'            => $this->type_name,
            'length_code'          => $this->length_code,
            'length_name'          => $this->length_name,
            'vin'                  => $this->vin,
            'owner_name'           => $this->owner_name,
            'body_color'           => $this->body_color,
            'approved_tonnage'     => $this->approved_tonnage,
            'actual_tonnage'       => $this->actual_tonnage,
        ];
    }
}
