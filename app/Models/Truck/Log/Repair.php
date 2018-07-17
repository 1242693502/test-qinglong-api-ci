<?php

namespace App\Models\Truck\Log;

/**
 * Class Repair
 *
 * @package App\Models\Truck\Log
 */
class Repair extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'truck_repair_logs';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '车辆维修保养记录表';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'truck_uuid',
        'driver_uuid',
        'order_uuid',
        'repair_type_id',
        'name',
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
