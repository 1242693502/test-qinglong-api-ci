<?php

namespace App\Http\Requests\InternalApi\Order\MainLine\Log;

class CreateReceiveReceiptRequest extends CreateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->withCommonRules([
            'contract_no'      => 'required|string|max:32',
            'contract_image'   => 'required|image_code',
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
            'contract_no'      => '随车合同编号',
            'contract_image'   => '随车合同照片',
            'receipt_images.*' => '随车单据照片',
        ]);
    }
}
