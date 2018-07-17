<?php

namespace App\Models\GasCard;

use App\Models\Model;
use Carbon\Carbon;
use QingLong\UUID\UUID;

class GasCardOrder extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'gas_card_orders';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '油卡加油记录表';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'gas_card_order_uuid',
        'truck_uuid',
        'gas_card_no',
        'total_price',
        'status',
        'approver_uuid',
        'approver_name',
        'approver_time',
        'approver_reason',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['approver_time'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (GasCardOrder $gasCardOrder) {
            //先设置删除标志,等待UUID填充后，清除删除标志
            $gasCardOrder->setAttribute('delete_time', Carbon::now());
        });

        static::created(function (GasCardOrder $gasCardOrder) {
            // 生成UUID并填充
            $gasCardOrderUUID = UUID::buildById(cons('uuid.gas_card_order'), $gasCardOrder->id);
            $gasCardOrderData = [
                'gas_card_order_uuid' => $gasCardOrderUUID,
                'delete_time'         => null
            ];
            $gasCardOrder->forceFill($gasCardOrderData)->save();
        });
    }
}