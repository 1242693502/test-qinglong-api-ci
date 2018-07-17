<?php

namespace App\Http\Requests\InternalApi\Order\MainLine\Log;

class CreateUnloadingAbnormalRequest extends CreateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->withCommonRules([
            'description' => 'required|string|max:255',
            'images.*'    => 'required|image_code',
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
            'description' => '描述',
            'images.*'    => '卸货异常照片',
        ]);
    }

}