<?php

namespace App\Http\Requests\InternalApi\Order\MainLine\Log;

class CheckTruckRequest extends CreateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->withCommonRules([
            'codes'    => 'present|array',
            'images.*' => 'nullable|image_code',
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
            'codes'    => '异常列表编码',
            'images.*' => '异常照片',
        ]);
    }

}