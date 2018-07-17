<?php

namespace App\Http\Requests\InternalApi\Truck\GasCard;

use App\Http\Requests\InternalApi\BaseRequest;

class LossRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'loss_reason' => 'required|string|max:255',
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
            'loss_reason' => '挂失原因',
        ];
    }
}
