<?php

namespace App\Http\Resources\InternalApi\Driver;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class DriverResource
 *
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Driver\Driver
 */
class DriverResource extends BaseResource
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
            'driver_uuid'          => $this->driver_uuid,
            'name'                 => $this->name,
            'job_number'           => $this->job_number,
            'phone'                => $this->phone,
            'phone_is_auth'        => $this->phone_is_auth,
            'id_number'            => $this->id_number,
            'id_number_is_auth'    => $this->id_number_is_auth,
            'driver_license_type'  => $this->driver_license_type,
            'qualification'        => $this->qualification,
            'contact_address_code' => $this->contact_address_code,
            'contact_address_name' => $this->contact_address_name,
            'contact_address'      => $this->contact_address,
            'audit_status'         => $this->audit_status,
            'open_account_time'    => $this->formatDate($this->open_account_time),
        ];
    }
}
