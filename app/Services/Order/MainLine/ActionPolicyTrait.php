<?php

namespace App\Services\Order\MainLine;

use App\Models\Order\MainLine\Attribute;
use App\Models\Order\MainLine\Place;
use App\Models\Order\OrderMainLine;

trait ActionPolicyTrait
{
    /**
     * @var string
     */
    protected $orderUUID;

    /**
     * @var \App\Models\Order\OrderMainLine|null
     */
    protected $orderMainLine = null;

    /**
     * @var \App\Models\Order\MainLine\Attribute|null
     */
    protected $orderMainLineAttribute = null;

    /**
     * ActionPolicyTrait constructor.
     *
     * @param string $orderUUID
     */
    public function __construct($orderUUID)
    {
        $this->orderUUID = $orderUUID;
    }

    /**
     * 获取订单详情
     *
     * @param bool $force
     *
     * @return \App\Models\Order\OrderMainLine|null
     */
    protected function orderMainLine($force = false)
    {
        if (is_null($this->orderMainLine) || $force) {
            $this->orderMainLine = OrderMainLine::where('order_uuid', $this->orderUUID)->first();
        }

        return $this->orderMainLine;
    }

    /**
     * 获取订单属性
     *
     * @param bool $force
     *
     * @return \App\Models\Order\MainLine\Attribute
     */
    protected function orderMainLineAttribute($force = false)
    {
        if (is_null($this->orderMainLineAttribute) || $force) {
            $this->orderMainLineAttribute = Attribute::where('order_uuid', $this->orderUUID)->first();
        }

        return $this->orderMainLineAttribute;
    }

    /**
     * 判断挂车是否已经检查过
     *
     * @return bool
     */
    protected function trailerIsChecked()
    {
        $orderMainLine = $this->orderMainLine();

        return $orderMainLine && $orderMainLine->trailer_plate;
    }

    /**
     * 判断是否已经完成了所有的装货地
     *
     * @return bool
     */
    protected function isFinishAllLoadingPlaces()
    {
        // 查询是否存在未完成的装货地
        $unfinishedLoadingPlace = Place::where('order_uuid', $this->orderUUID)
            ->where('type', cons('order.mainline.place.type.loading'))
            ->whereNull('departure_time')
            ->first(['id']);

        return !($unfinishedLoadingPlace && $unfinishedLoadingPlace->exists);
    }

    /**
     * 判断是否已经完成所有单据交接（卸货地）
     *
     * @return bool
     */
    protected function isFinishSendReceipt()
    {
        $receiptStatuses = Attribute::where('order_uuid', $this->orderUUID)
            ->whereNotNull('receipt_statuses')
            ->value('receipt_statuses');

        return collect($receiptStatuses)->every(function ($value) {
            return (int)$value === 1;
        });
    }
}