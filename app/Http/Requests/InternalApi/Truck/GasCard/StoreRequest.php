<?php

namespace App\Http\Requests\InternalApi\Truck\GasCard;

use App\Http\Requests\InternalApi\BaseRequest;

class StoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'gas_card_no' => 'required|string|max:32',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'gas_card_no' => '油卡卡号',
        ];
    }
}
