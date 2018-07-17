<?php

namespace App\Models\Truck\Log;

/**
 * Class Refueling
 *
 * @package App\Models\Truck\Log
 */
class Refuel extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'truck_refuel_logs';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '车辆成本 - 加油记录表';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'truck_uuid',
        'driver_uuid',
        'order_uuid',
        'per_price',
        'liter_number',
        'total_price',
        'pay_type_id',
        'gas_card_no',
        'current_mileage',
        'current_mileage_image',
        'reg_time',
        'status',
        'images',
        'has_invoice',
        'merchant_name',
        'longitude',
        'latitude',
        'remark',
    ];
}
