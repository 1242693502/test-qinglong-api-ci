<?php

namespace App\Http\Requests\InternalApi\GasCard\GasCardOrder;

use App\Http\Requests\InternalApi\BaseRequest;

class ApprovalRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status'          => 'required',
            'approver_uuid'   => 'required|string|max:32',
            'approver_name'   => 'required|string|max:16',
            'approver_reason' => '',
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
            'status'          => '审核状态',
            'approver_uuid'   => '审核人UUID',
            'approver_name'   => '审核人姓名',
            'approver_reason' => '审核理由',
        ];
    }
}
