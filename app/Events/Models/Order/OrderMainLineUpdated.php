<?php

namespace App\Events\Models\Order;

use App\Models\Order\OrderMainLine;
use Illuminate\Queue\SerializesModels;

class OrderMainLineUpdated
{
    use SerializesModels;

    /**
     * @var \App\Models\Order\OrderMainLine $order
     */
    public $order;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Order\OrderMainLine $order
     */
    public function __construct(OrderMainLine $order)
    {
        $this->order = $order;
    }
}
