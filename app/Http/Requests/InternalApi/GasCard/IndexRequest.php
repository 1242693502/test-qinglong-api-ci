<?php

namespace App\Http\Requests\InternalApi\GasCard;

use App\Http\Requests\InternalApi\BaseRequest;

class IndexRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'gas_card_no' => 'max:32',
            'page'        => '',
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
            'page'        => '分页数',
        ];
    }
}
