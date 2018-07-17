<?php

namespace App\Http\Requests\InternalApi\Truck;

use App\Http\Requests\InternalApi\BaseRequest;

class SwapDrivingRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'driver_uuid'    => 'required|string|max:32',
            'has_exceptions' => 'required|boolean',
            'images.*'       => 'present|image_code',
            'longitude'      => 'nullable|numeric',
            'latitude'       => 'nullable|numeric',
            'remark'         => 'nullable|string|max:128',
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
            'driver_uuid'    => '司机UUID',
            'has_exceptions' => '是否存在异常情况',
            'images.*'       => '图片',
            'longitude'      => '维度',
            'latitude'       => '经度',
            'remark'         => '备注',
        ];
    }
}
