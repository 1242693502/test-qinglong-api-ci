<?php

namespace App\Models\Truck\Log;

/**
 * Class AdBlue
 *
 * @package App\Models\Truck\Log
 */
class AdBlue extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'truck_adblue_logs';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '车辆尿素领用记录表';

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
