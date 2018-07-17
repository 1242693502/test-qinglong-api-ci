<?php

namespace App\Http\Requests\InternalApi\Truck\Log;

use App\Http\Requests\InternalApi\BaseRequest;

class CreateRefuelRequest extends BaseRequest
{
    use LogTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->withCommonRules([
            'per_price'          => 'required|ql_int',
            'liter_number'       => 'required|ql_int',
            'total_price'        => 'nullable|ql_int|min:1',
            'pay_type'           => 'required|string|max:32',
            'filling_station_id' => 'nullable|ql_int',
            'gas_card_no'        => 'nullable|string|max:32',
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->withCommonAttributes([
            'per_price'          => '单价',
            'liter_number'       => '升数',
            'total_price'        => '总费用',
            'pay_type'           => '付款方式',
            'filling_station_id' => '加油站ID',
            'gas_card_no'        => '油卡卡号',
        ]);
    }
}
