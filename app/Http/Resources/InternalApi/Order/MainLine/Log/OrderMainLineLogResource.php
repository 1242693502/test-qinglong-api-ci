<?php

namespace App\Http\Resources\InternalApi\Order\MainLine\Log;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class OrderMainLineLogResource
 *
 * @package App\Http\Resources
 *
 * @mixin \App\Models\Order\MainLine\Log
 */
class OrderMainLineLogResource extends BaseResource
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
            'order_uuid'   => $this->order_uuid,
            'order_status' => $this->order_status,
            'type'         => $this->type,
            'title'        => $this->title,
            'status'       => $this->status,
            'remark'       => $this->remark,
            'longitude'    => $this->longitude,
            'latitude'     => $this->latitude,
            'contents'     => $this->contents,
        ];
    }
}
