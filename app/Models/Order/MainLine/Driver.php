<?php

namespace App\Models\Order\MainLine;

use App\Models\Model;

/**
 * Class Driver
 *
 * @package App\Models\Order\MainLine
 */
class Driver extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'order_mainline_driver';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '专线订单司机关联表';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'order_uuid',
        'driver_uuid',
        'driver_name',
        'driver_phone',
        'type',
        'status', //预留，暂时都设置为1
        'note',
        'confirm_time',
    ];

    /**
     * 需要转为日期的字段
     *
     * @var array
     */
    protected $dates = [
        'confirm_time',
        'complete_time',
    ];


}
