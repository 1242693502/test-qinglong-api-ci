<?php

namespace App\Http\Requests\InternalApi\Order\MainLine\Log;


class CreateTrafficJamRequest extends CreateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->withCommonRules([
            'images.*' => 'required|image_code',
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
            'images.*' => '堵车照片',
        ]);
    }
}
