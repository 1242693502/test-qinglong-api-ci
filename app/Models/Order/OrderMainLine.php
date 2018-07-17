<?php

namespace App\Models\Order;

use App\Events\Models\Order\OrderMainLineUpdated;
use App\Models\Model;
use App\Models\Trailer\Trailer;
use App\Models\Truck\Truck;
use Carbon\Carbon;
use QingLong\UUID\UUID;

/**
 * Class MainLine
 *
 * @package App\Models\Order\MainLine
 */
class OrderMainLine extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'order_mainlines';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '专线订单';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'out_trade_no',
        'contract_no',
        'shipper_name',
        'shipper_user_name',
        'shipper_user_phone',
        'origin_city_code',
        'origin_city_name',
        'destination_city_code',
        'destination_city_name',
        'transport_no',
        'goods_name',
        'goods_weight_appointment',
        'goods_volume_appointment',
        'order_notes',
        'order_time',
        'departure_time_appointment',
        'truck_plate_appointment',
        'trailer_plate_appointment',
        'order_status',
        'goods_weight',
        'goods_volume',
        'truck_uuid',
        'trailer_uuid',
        'truck_plate',
        'trailer_plate',
    ];

    /**
     * 需要转为日期的字段
     *
     * @var array
     */
    protected $dates = [
        'order_time',
        'departure_time_appointment',
        'confirm_time',
        'complete_time',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'updated' => OrderMainLineUpdated::class,
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (OrderMainLine $orderMainLine)
        {
            // 订单初始状态设置为0
            $orderMainLine->setAttribute('order_status', cons('order.mainline.status.uncreated'));
            // 设置订单时间
            $orderMainLine->setAttribute('order_time', Carbon::now());
        });

        static::created(function (OrderMainLine $orderMainLine)
        {
            // 生成订单UUID并填充
            $orderUUID         = UUID::buildById(cons('uuid.order'), $orderMainLine->id);
            $orderMainLineData = [
                'order_uuid' => $orderUUID,
            ];
            $orderMainLine->forceFill($orderMainLineData)->save();
        });
    }

    /**
     * 关联承运货车
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function orderTruck()
    {
        return $this->hasOne(Truck::class, 'truck_uuid', 'truck_uuid');
    }

    /**
     * 关联承运挂车
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function orderTrailer()
    {
        return $this->hasOne(Trailer::class, 'trailer_uuid', 'trailer_uuid');
    }
}
