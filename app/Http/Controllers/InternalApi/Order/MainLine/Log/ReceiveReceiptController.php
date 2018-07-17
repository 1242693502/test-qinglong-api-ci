<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateReceiveReceiptRequest;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\ReceiveReceiptService;
use Illuminate\Support\Arr;

/**
 * Class ReceiveReceiptController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class ReceiveReceiptController extends BaseController
{
    /**
     * 记录交接单据 - 收
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateReceiveReceiptRequest $request
     * @param string                                                                        $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateReceiveReceiptRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new ReceiveReceiptService())->create($orderUUID, $driverUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }
}
