<?php

namespace App\Http\Controllers\InternalApi\Order\MainLine;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Order\MainLine\ActionResource;
use App\Services\Order\MainLine\ActionService;

class ActionController extends BaseController
{

    /**
     * 获取当前订单可操作的列表
     *
     * @param string $orderUUID
     *
     * @return \App\Http\Resources\InternalApi\Order\MainLine\ActionResource[]
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \InvalidArgumentException
     */
    public function getOrderActions($orderUUID)
    {
        $actions = ActionService::serviceForOrderUUID($orderUUID)->actions();
        return ActionResource::collection($actions);
    }
}
