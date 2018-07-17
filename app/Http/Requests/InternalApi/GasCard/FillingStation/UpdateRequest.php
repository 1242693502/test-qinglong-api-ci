<?php

namespace App\Http\Requests\InternalApi\GasCard\FillingStation;

use App\Http\Requests\InternalApi\BaseRequest;

class UpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'      => 'required|string|max:255|unique:filling_stations,name,' . $this->route('stationID'),
            'area_code' => 'required|string|max:10',
            'address'   => 'required|string|max:256',
            'longitude' => 'nullable|numeric',
            'latitude'  => 'nullable|numeric',
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
            'name'      => '加油站名称',
            'area_code' => '最后一级地址编码',
            'address'   => '详细地址（不包括省市区街道）',
            'longitude' => '位置经度',
            'latitude'  => '位置维度',
        ];
    }
}