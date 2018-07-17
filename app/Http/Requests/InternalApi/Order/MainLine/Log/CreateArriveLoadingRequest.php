<?php

namespace App\Http\Requests\InternalApi\Order\MainLine\Log;


class CreateArriveLoadingRequest extends CreateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->withCommonRules([
            'place_uuid' => 'required|string|max:32',
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
            'place_uuid' => '装货地址UUID',
        ]);
    }
}
