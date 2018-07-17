<?php

namespace App\Http\Requests\InternalApi\Order\MainLine\Log;

class CreateRecordWeightRequest extends CreateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->withCommonRules([
            'goods_weight' => 'required|ql_int',
            'total_price'  => 'required|ql_int',
            'images.*'     => 'required|image_code',
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
            'goods_weight' => '过磅重量',
            'total_price'  => '总价',
            'images.*'     => '过磅照片',
        ]);
    }
}