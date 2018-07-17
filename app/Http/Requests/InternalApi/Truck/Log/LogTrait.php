<?php

namespace App\Http\Requests\InternalApi\Truck\Log;


trait LogTrait
{
    /**
     * 通用规则
     *
     * @var array
     */
    private $commonRules = [
        'driver_uuid'           => 'required|string|max:32',
        'images.*'              => 'present|image_code',
        'has_invoice'           => 'required|bool',
        'merchant_name'         => 'nullable|string|max:32',
        'longitude'             => 'nullable|numeric',
        'latitude'              => 'nullable|numeric',
        'remark'                => 'nullable|string|max:128',
        'current_mileage'       => 'nullable|ql_int',
        'current_mileage_image' => 'required_with:current_mileage|nullable|image_code',
    ];

    /**
     * 通用翻译
     *
     * @var array
     */
    private $commonAttributes = [
        'driver_uuid'           => '司机UUID',
        'images.*'                => '图片',
        'has_invoice'           => '是否有发票',
        'merchant_name'         => '商户名称',
        'longitude'             => '维度',
        'latitude'              => '经度',
        'remark'                => '备注',
        'current_mileage'       => '当前里程',
        'current_mileage_image' => '里程照片',
    ];

    /**
     * 通用规则
     *
     * @param array $rules
     *
     * @return array
     */
    private function withCommonRules(array $rules = [])
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
    private function withCommonAttributes(array $attributes = [])
    {
        return array_merge($this->commonAttributes, $attributes);
    }
}