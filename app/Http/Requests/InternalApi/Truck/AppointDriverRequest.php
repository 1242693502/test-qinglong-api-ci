<?php

namespace App\Http\Requests\InternalApi\Truck;

use App\Http\Requests\InternalApi\BaseRequest;

class AppointDriverRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'main_driver_uuid'       => 'required',
            'other_driver_uuid_list' => '',
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
            'main_driver_uuid'       => '主司机UUID',
            'other_driver_uuid_list' => '副司机UUID集合',
        ];
    }
}
