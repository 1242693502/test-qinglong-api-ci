<?php

namespace App\Http\Resources\InternalApi\Trailer;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class TrailerResource
 *
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Trailer\Trailer
 */
class TrailerResource extends BaseResource
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
            'trailer_uuid'         => $this->trailer_uuid,
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
            'actual_tonnage'       => $this->actual_tonnage
        ];
    }
}
