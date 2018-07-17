<?php

namespace App\Http\Requests\InternalApi\Order\MainLine\Log;


class CreateSendReceiptRequest extends CreateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->withCommonRules([
            'receipt_images.*' => 'required|image_code',
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
            'receipt_images.*' => '随车单据照片',

        ]);
    }
}
