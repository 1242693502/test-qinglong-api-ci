<?php

namespace App\Http\Requests\InternalApi\Order\MainLine;

use App\Http\Requests\InternalApi\BaseRequest;

class AppointTruckRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'truck_uuid' => 'required|max:32',
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
            'truck_uuid' => '车辆UUID',
        ];
    }
}
