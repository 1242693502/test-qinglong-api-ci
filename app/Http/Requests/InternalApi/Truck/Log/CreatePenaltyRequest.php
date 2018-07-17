<?php

namespace App\Http\Requests\InternalApi\Truck\Log;

use App\Http\Requests\InternalApi\BaseRequest;

class CreatePenaltyRequest extends BaseRequest
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
            'penalty_date'   => 'required|date_format:Y-m-d',
            'penalty_points' => 'required|ql_int',
            'total_price'    => 'required|ql_int|min:1',
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
            'penalty_date'   => '违章日期',
            'penalty_points' => '扣分',
            'total_price'    => '总费用',
        ]);
    }
}
