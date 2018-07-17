<?php

namespace App\Http\Requests\InternalApi\Order\MainLine;

use App\Http\Requests\InternalApi\BaseRequest;

class DriverConfirmRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'driver_uuid' => 'required|max:32',
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
            'driver_uuid' => '司机UUID',
        ];
    }
}
