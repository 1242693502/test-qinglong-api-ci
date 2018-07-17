<?php

namespace App\Http\Resources\InternalApi\Truck;

use App\Http\Resources\InternalApi\BaseResource;

class TruckGasCardResource extends BaseResource
{
    public function toArray($request)
    {
        return [
            'truck_uuid'    => $this->truck_uuid,
            'gas_card_no'   => $this->gas_card_no,
            'channel'       => $this->channel,
            'status'        => $this->status,
            'status_name'   => cons()->valueLang('truck.gas_card.status', $this->status),
            'bind_time'     => $this->formatDate($this->bind_time),
            'unbind_time'   => $this->formatDate($this->unbind_time),
            'loss_time'     => $this->formatDate($this->loss_time),
            'unbind_reason' => $this->unbind_reason,
            'loss_reason'   => $this->loss_reason,
        ];
    }
}
