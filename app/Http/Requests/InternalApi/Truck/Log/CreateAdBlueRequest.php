<?php

namespace App\Http\Requests\InternalApi\Truck\Log;

use App\Http\Requests\InternalApi\BaseRequest;

class CreateAdBlueRequest extends BaseRequest
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
        ]);
    }
}
