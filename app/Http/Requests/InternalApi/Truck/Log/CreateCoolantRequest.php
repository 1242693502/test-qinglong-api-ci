<?php

namespace App\Http\Requests\InternalApi\Truck\Log;

use App\Http\Requests\InternalApi\BaseRequest;

class CreateCoolantRequest extends BaseRequest
{
    use LogTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->withCommonRules([
            'liter_number' => 'required|ql_int',
            'total_price'  => 'required|ql_int|min:1',
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
            'liter_number' => '升数',
            'total_price'  => '总费用',
        ]);
    }
}
