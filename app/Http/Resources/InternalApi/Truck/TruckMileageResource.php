<?php

namespace App\Http\Resources\InternalApi\Truck;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class TruckMileageResource
 *
 * @package App\Http\Resources\DriverApi\V1\Truck
 */
class TruckMileageResource extends BaseResource
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
            'mileage' => $this['mileage'],
        ];
    }
}
