<?php

namespace App\Http\Resources\InternalApi\Truck\Log;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class PenaltyResource
 *
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Truck\Log\Penalty
 */
class PenaltyResource extends BaseResource
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
            'truck_uuid'     => $this->truck_uuid,
            'penalty_date'   => $this->penalty_date,
            'penalty_points' => $this->penalty_points,
            'total_price'    => $this->total_price,
            'images'         => $this->images,
            'remark'         => $this->remark,
            'longitude'      => $this->longitude,
            'latitude'       => $this->latitude,
        ];
    }
}
