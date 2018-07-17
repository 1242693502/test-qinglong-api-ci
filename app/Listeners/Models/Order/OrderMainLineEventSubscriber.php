<?php

namespace App\Listeners\Models\Order;

use App\Events\Models\Order\OrderMainLineUpdated;
use QingLong\Platform\Order\Order;

class OrderMainLineEventSubscriber
{
    /**
     * 订单更新事件
     *
     * @param OrderMainLineUpdated $event
     */
    public function onUpdated($event)
    {
        //更新订单后，将订单状态同步回订单系统
        (new Order())->updateOrderMainLineStatus($event->order->out_trade_no, $event->order->order_status);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $currentClass = get_class();

        $events->listen(OrderMainLineUpdated::class, $currentClass . '@onUpdated');
    }

}