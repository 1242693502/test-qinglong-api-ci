<?php

namespace App\Http\Requests\InternalApi\Truck\Log;

use App\Http\Requests\InternalApi\BaseRequest;

class CreateOtherRequest extends BaseRequest
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
            'name'        => 'required|string|max:32',
            'total_price' => 'required|ql_int|min:1',
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
            'name'        => '名称',
            'total_price' => '总费用',
        ]);
    }
}
