<?php

namespace App\Http\Resources\InternalApi\GasCard;

use App\Http\Resources\InternalApi\BaseResource;

class GasCardOrderResource extends BaseResource
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
            'gas_card_order_uuid' => $this->gas_card_order_uuid,
            'gas_card_no'         => $this->gas_card_no,
            'total_price'         => $this->total_price,
            'status'              => $this->status,
            'status_name'         => cons()->valueLang('truck.gas_card.order', $this->status),
            'approver_uuid'       => $this->approver_uuid,
            'approver_name'       => $this->approver_name,
            'approver_time'       => $this->formatDate($this->approver_time),
            'approver_reason'     => $this->approver_reason,
        ];
    }
}
