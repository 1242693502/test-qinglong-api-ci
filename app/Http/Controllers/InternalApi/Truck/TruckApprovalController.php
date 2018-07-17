<?php

namespace App\Http\Controllers\InternalApi\Truck;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Truck\TruckApproval\ApprovalRequest;
use App\Http\Resources\InternalApi\Truck\TruckApprovalResource;
use App\Services\Truck\TruckApprovalService;

class TruckApprovalController extends BaseController
{
    /**
     * 审核订单车辆检查
     *
     * @param ApprovalRequest $request
     * @param int             $truckApprovalId
     *
     * @return \App\Http\Resources\InternalApi\Truck\TruckApprovalResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     */
    public function approval(ApprovalRequest $request, $truckApprovalId)
    {
        $inputs         = $request->validated();
        $truckApprovals = (new TruckApprovalService())->approval($truckApprovalId, $inputs);
        return new TruckApprovalResource($truckApprovals);
    }
}