<?php

namespace App\Http\Resources\InternalApi\Driver;

use App\Http\Resources\InternalApi\BaseResource;
use App\Http\Resources\InternalApi\Truck\TruckResource;

/**
 * Class DriverTruckResource
 *
 * @package App\Http\Resources\InternalApi\Driver
 *
 * @mixin \App\Models\Driver\DriverTruck
 */
class DriverTruckResource extends BaseResource
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
            'driver_uuid' => $this->driver_uuid,
            'driver'      => new DriverResource($this->whenLoaded('driver')),
            'is_driving'  => $this->is_driving,
            'truck_uuid'  => $this->truck_uuid,
            'truck'       => new TruckResource($this->whenLoaded('truck')),
            'reg_time'    => $this->formatDate($this->reg_time),
            'remark'      => $this->remark,
        ];
    }
}
