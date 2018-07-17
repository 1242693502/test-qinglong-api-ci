<?php

namespace App\Http\Resources\InternalApi\Truck;

use App\Http\Resources\InternalApi\BaseResource;

class TruckApprovalResource extends BaseResource
{
    public function toArray($request)
    {
        return [
            'truck_uuid'      => $this->truck_uuid,
            'driver_uuid'     => $this->driver_uuid,
            'order_uuid'      => $this->order_uuid,
            'type'            => $this->type,
            'type_name'       => $this->type_name,
            'remark'          => $this->remark,
            'status'          => $this->status,
            'status_name'     => cons()->valueLang('truck.approval.status', $this->status),
            'approver_uuid'   => $this->approver_uuid,
            'approver_name'   => $this->approver_name,
            'approver_time'   => $this->formatDate($this->approver_time),
            'approver_reason' => $this->approver_reason,
            'contents'        => $this->contents,
        ];
    }
}
