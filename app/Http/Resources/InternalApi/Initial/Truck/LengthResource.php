<?php

namespace App\Http\Resources\InternalApi\Initial\Truck;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class LengthResource
 *
 * @package App\Http\Resources\InternalApi\Initial\Truck
 */
class LengthResource extends BaseResource
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
            'code' => $this['code'],
            'name' => $this['name'],
            'type' => $this['type'],
        ];
    }
}
