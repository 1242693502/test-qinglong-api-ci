<?php

namespace App\Http\Requests\InternalApi\Order\MainLine\Log;


class CheckTrailerCertificatesRequest extends CreateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->withCommonRules([
            'codes'         => 'present|array',
            'trailer_plate' => 'required|license_plate_number:trailer',
            'images.*'      => 'present|image_code',
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
            'codes'         => '缺失证件code列表',
            'trailer_plate' => '挂车车牌号',
            'images.*'      => '图片列表',
        ]);
    }
}
