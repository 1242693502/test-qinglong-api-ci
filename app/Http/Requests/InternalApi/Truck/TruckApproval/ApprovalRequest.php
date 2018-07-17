<?php

namespace App\Http\Requests\InternalApi\Truck\TruckApproval;

use App\Http\Requests\InternalApi\BaseRequest;

class ApprovalRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'status'          => 'required',
            'approver_uuid'   => 'required|string|max:32',
            'approver_name'   => 'required|string|max:16',
            'approver_reason' => '',
        ];
    }

    public function attributes()
    {
        return [
            'status'          => '审批结果',
            'approver_uuid'   => '审批人UUID',
            'approver_name'   => '审批人姓名',
            'approver_reason' => '审批原因',
        ];
    }
}
