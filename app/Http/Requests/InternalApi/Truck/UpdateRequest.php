<?php

namespace App\Http\Requests\InternalApi\Truck;

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
            'belong_type'      => 'required|ql_int|max:255',
            'brand'            => 'required|string|max:12',
            'engine_number'    => 'required|string|max:32',
            'axle_number'      => 'required|integer|ql_int|between:1,99',
            'type_code'        => 'required|truck_type_code',
            'length_code'      => 'required|truck_length_code',
            'vin'              => 'required|string|max:17',
            'owner_name'       => 'required|string|max:16',
            'body_color'       => 'required|string|max:3',
            'approved_tonnage' => 'required|ql_int',
            'actual_tonnage'   => 'required|ql_int',
            'certificates'     => '',
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
            'belong_type'      => '归属类型',
            'brand'            => '车辆品牌',
            'engine_number'    => '发动机号码',
            'axle_number'      => '车轴数',
            'type_code'        => '车型编码',
            'length_code'      => '车长编码',
            'vin'              => '车架号',
            'owner_name'       => '车辆车主姓名',
            'quality'          => '车辆成色',
            'body_color'       => '车身颜色',
            'approved_tonnage' => '牵引货物吨位',
            'actual_tonnage'   => '运营货物吨位',
            'company_name'     => '车辆所属公司',
            'certificates'     => '证件照列表',
        ];
    }
}
