<?php

namespace App\Http\Requests\InternalApi\Order\MainLine\Log;

class CreateRecordSealsRequest extends CreateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->withCommonRules([
            'seal_first_no'     => 'required|string|max:32',
            'seal_first_image'  => 'required|image_code',
            'seal_second_no'    => 'required|string|max:32',
            'seal_second_image' => 'required|image_code',
            'seal_last_no'      => 'required|string|max:32',
            'seal_last_image'   => 'required|image_code',
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
            'seal_first_no'     => '边门封签号1',
            'seal_first_image'  => '边门封签号1照片',
            'seal_second_no'    => '边门封签号2',
            'seal_second_image' => '边门封签号2照片',
            'seal_last_no'      => '尾门封签号',
            'seal_last_image'   => '尾门封签号照片',
        ]);
    }
}
