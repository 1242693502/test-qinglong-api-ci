<?php

namespace App\Models\Driver;

use App\Models\Model;

/**
 * Class DriverTruckLog
 *
 * @package App\Models\Driver
 */
class DriverTruckLog extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'driver_truck_logs';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '司机关联记录';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'driver_uuid',
        'is_driving',
        'truck_uuid',
        'remark',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_driving' => 'bool',
    ];
}