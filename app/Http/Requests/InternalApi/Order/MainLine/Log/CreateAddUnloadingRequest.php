<?php

namespace App\Http\Requests\InternalApi\Order\MainLine\Log;


class CreateAddUnloadingRequest extends CreateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->withCommonRules([
            'area_code'             => 'required|string|max:10',
            'address'               => 'required|string|max:256',
            'address_contact_name'  => 'required|string|max:8',
            'address_contact_phone' => 'required|phone_number',
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
            'area_code'             => '卸货地址最后地址编码',
            'address'               => '除省市区外的详细地址',
            'address_contact_name'  => '联系人姓名',
            'address_contact_phone' => '联系人手机号码',
        ]);
    }
}
