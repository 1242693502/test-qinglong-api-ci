<?php

namespace App\Http\Requests\InternalApi\Order\MainLine\Log;

use App\Http\Requests\InternalApi\BaseRequest;

class CreateRequest extends BaseRequest
{
    /**
     * 通用规则
     *
     * @var array
     */
    private $commonRules = [
        'driver_uuid'           => 'required|string|max:32',
        'remark'                => 'nullable|string|max:128',
        'current_mileage'       => 'nullable|ql_int',
        'current_mileage_image' => 'required_with:current_mileage|nullable|image_code',
        'longitude'             => 'nullable|numeric',
        'latitude'              => 'nullable|numeric',
    ];

    /**
     * 通用翻译
     *
     * @var array
     */
    private $commonAttributes = [
        'driver_uuid'           => '司机UUID',
        'remark'                => '备注',
        'current_mileage'       => '当前里程',
        'current_mileage_image' => '里程照片',
        'longitude'             => '纬度',
        'latitude'              => '经度',
    ];

    /**
     * 通用规则
     *
     * @param array $rules
     *
     * @return array
     */
    protected function withCommonRules(array $rules = [])
    {
        return array_merge($this->commonRules, $rules);
    }

    /**
     * 通用属性
     *
     * @param array $attributes
     *
     * @return array
     */
    protected function withCommonAttributes(array $attributes = [])
    {
        return array_merge($this->commonAttributes, $attributes);
    }

}
