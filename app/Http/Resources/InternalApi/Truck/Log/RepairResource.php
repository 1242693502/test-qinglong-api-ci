<?php

namespace App\Http\Resources\InternalApi\Truck\Log;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class RepairResource
 *
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Truck\Log\Repair
 */
class RepairResource extends BaseResource
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
            'truck_uuid'     => $this->truck_uuid,
            'repair_type_id' => $this->repair_type_id,
            'name'           => $this->name,
            'total_price'    => $this->total_price,
            'images'         => $this->images,
            'merchant_name'  => $this->merchant_name,
            'remark'         => $this->remark,
            'longitude'      => $this->longitude,
            'latitude'       => $this->latitude
        ];
    }
}
