<?php

namespace App\Models\Truck\Log;

/**
 * Class Coolant
 *
 * @package App\Models\Truck\Log
 */
class Coolant extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'truck_coolant_logs';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '车辆加水记录表';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'truck_uuid',
        'driver_uuid',
        'order_uuid',
        'liter_number',
        'total_price',
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
