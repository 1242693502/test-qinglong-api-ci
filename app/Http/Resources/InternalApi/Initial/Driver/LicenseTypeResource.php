<?php

namespace App\Http\Resources\InternalApi\Initial\Driver;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class LicenseTypeResource
 *
 * @package App\Http\Resources\InternalApi\Initial\Driver
 */
class LicenseTypeResource extends BaseResource
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
