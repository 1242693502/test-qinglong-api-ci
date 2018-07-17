<?php

namespace App\Services\Truck;

use App\Models\Truck\Truck;
use App\Models\Truck\TruckApproval;
use App\Services\BaseService;
use Carbon\Carbon;
use Urland\Exceptions\Client\BadRequestException;
use Urland\Exceptions\Client\ForbiddenException;

class TruckApprovalService extends BaseService
{
    /**
     * 订单车辆审核
     *
     * @param int   $truckApprovalId
     * @param array $approvalData
     *
     * @return mixed
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function approval($truckApprovalId, array $approvalData)
    {
        $statusList = cons('truck.approval.status');
        $status     = $approvalData['status'];
        if (!in_array($status, [/*$statusList['cancel'],*/
                                $statusList['success']
        ])) {
            throw new BadRequestException('审核状态错误');
        }
        if ($status == $statusList['cancel'] && empty($approvalData['approver_reason'])) {
            throw new BadRequestException('审核不通过时必须填写原因');
        }

        $truckApprovals = TruckApproval::find($truckApprovalId);
        // 判断状态
        if ($truckApprovals->status !== $statusList['waiting']) {
            throw  new ForbiddenException('当前订单审核状态异常');
        }

        $trucks = Truck::where('truck_uuid', $truckApprovals->truck_uuid)->firstOrFail();

        $fillData = [
            'status'          => $status,
            'approver_uuid'   => $approvalData['approver_uuid'],
            'approver_name'   => $approvalData['approver_name'],
            'approver_reason' => $approvalData['approver_reason'],
            'approver_time'   => Carbon::now(),
        ];
        $truckApprovals->fill($fillData)->save();

        if ($status == $statusList['success']) {
            // 修改车辆状态为可用
            $trucks->fill(['is_available' => 1])->save();
        }

        return $truckApprovals;
    }
}