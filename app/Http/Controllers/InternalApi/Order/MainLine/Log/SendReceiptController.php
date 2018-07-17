<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine\Log;

use App\Http\Requests\InternalApi\Order\MainLine\Log\CreateSendReceiptRequest;
use App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource;
use App\Services\Order\MainLine\Log\SendReceiptService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;

/**
 * Class SendReceiptController
 *
 * @package App\Http\Controllers\InternalApi\Order\MainLine\Log
 */
class SendReceiptController extends Controller
{
    /**
     * 交接单据 - 给
     *
     * @param \App\Http\Requests\InternalApi\Order\MainLine\Log\CreateSendReceiptRequest $request
     * @param                                                                            $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\Log\OrderMainLineLogResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateSendReceiptRequest $request, $orderUUID)
    {
        $inputs     = $request->validated();
        $driverUUID = Arr::pull($inputs, 'driver_uuid');

        $orderMainLineLog = (new SendReceiptService())->create($orderUUID, $driverUUID, $inputs);
        return new OrderMainLineLogResource($orderMainLineLog);
    }
}
