<?php

namespace App\Http\Resources\InternalApi\Notification;

use App\Http\Resources\InternalApi\BaseResource;

/**
 * Class NotificationResource
 *
 * @package App\Http\Resources\InternalApi\Notification
 *
 * @mixin \App\Models\Notification\Notification
 */
class NotificationResource extends BaseResource
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
            'to_uuid'     => $this->to_uuid,
            'to_type'     => $this->to_type,
            'to_name'     => $this->to_name,
            'from_uuid'   => $this->from_uuid,
            'from_type'   => $this->from_type,
            'from_name'   => $this->from_name,
            'type'        => $this->type,
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status,
            'contents'    => $this->contents,
        ];
    }

}