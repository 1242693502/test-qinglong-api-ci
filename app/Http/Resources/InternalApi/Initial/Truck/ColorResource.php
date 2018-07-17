<?php

namespace App\Http\Resources\InternalApi\Initial\Truck;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class ColorResource
 *
 * @package App\Http\Resources\InternalApi\Initial\Truck
 */
class ColorResource extends BaseResource
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
            'code' => $this['name'],
            'name' => $this['name'],
        ];
    }
}
