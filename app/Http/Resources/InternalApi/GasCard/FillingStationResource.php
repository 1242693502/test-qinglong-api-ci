<?php

namespace App\Http\Resources\InternalApi\GasCard;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class FillingStationResource
 *
 * @package App\Http\Resources\InternalApi\GasCard
 *
 * @mixin \App\Models\GasCard\FillingStation
 */
class FillingStationResource extends BaseResource
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
            'id'        => $this->id,
            'name'      => $this->name,
            'area_code' => $this->area_code,
            'area_name' => $this->area_name,
            'address'   => $this->address,
            'longitude' => $this->longitude,
            'latitude'  => $this->latitude,
        ];
    }
}
