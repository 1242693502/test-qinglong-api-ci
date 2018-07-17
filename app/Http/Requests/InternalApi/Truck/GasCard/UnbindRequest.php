<?php

namespace App\Http\Requests\InternalApi\Truck\GasCard;

use App\Http\Requests\InternalApi\BaseRequest;

class UnbindRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'unbind_reason' => 'required|string|max:255',
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
            'unbind_reason' => '解绑原因',
        ];
    }
}
