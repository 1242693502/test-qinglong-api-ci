<?php

namespace App\Http\Requests\InternalApi\Order\MainLine\Log;

class CheckTruckCertificatesRequest extends CreateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->withCommonRules([
            'codes' => 'present|array',
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
            'codes' => '缺失证件列表编码',
        ]);
    }
}