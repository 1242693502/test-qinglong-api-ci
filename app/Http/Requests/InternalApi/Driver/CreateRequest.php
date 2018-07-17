<?php

namespace App\Http\Requests\InternalApi\Driver;

use App\Http\Requests\InternalApi\BaseRequest;
use QingLong\Validator\IDCard;

class CreateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'                 => 'required|string|max:16',
            'job_number'           => 'required|string|max:32',
            'phone'                => 'required|phone_number|max:11',
            'id_number'            => [
                'required',
                'string',
                'max:18',
                function ($attribute, $value, $fail) {
                    if (!IDCard::validateIDCard($value)) {
                        return $fail('不是有效的身份证号');
                    }
                }
            ],
            'driver_license_type'  => 'required|string|max:2',
            'qualification'        => 'required|string|max:32',
            'contact_address_code' => 'required|string|max:10',
            'contact_address'      => 'max:255',
            'certificates'         => '',
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
            'name'                 => '姓名',
            'job_number'           => '工号',
            'phone'                => '手机号码',
            'id_number'            => '身份证号',
            'driver_license_type'  => '驾照类型',
            'qualification'        => '从业资格证',
            'contact_address_code' => '联系地址镇/街道级编码',
            'contact_address'      => '联系地址',
            'certificates'         => '证件照列表',
        ];
    }
}
