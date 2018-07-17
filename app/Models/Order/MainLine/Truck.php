<?php

namespace App\Models\Order\MainLine;

use App\Models\Model;

/**
 * Class Truck
 *
 * @package App\Models\Order\MainLine
 */
class Truck extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'order_mainline_truck';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '订单状态';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'order_uuid',
        'truck_uuid',
        'truck_plate',
        'status',
        'note'
    ];

    /**
     * 需要转为日期的字段
     *
     * @var array
     */
    protected $dates = [

    ];


}
