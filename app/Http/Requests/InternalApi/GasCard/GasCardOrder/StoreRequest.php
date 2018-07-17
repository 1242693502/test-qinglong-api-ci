<?php

namespace App\Http\Requests\InternalApi\GasCard\GasCardOrder;

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
            'total_price' => 'required|integer|ql_int|between:100,9999900',
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
            'total_price' => '充值金额',
        ];
    }
}
