<?php

namespace App\Models\Order\MainLine;

use App\Models\Model;

/**
 * Class Status
 *
 * @package App\Models\Order\MainLine
 */
class Status extends Model
{

    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'order_mainline_statuses';

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
        'order_status',
        'action_flag',
        'note',
    ];

    /**
     * 需要转为日期的字段
     *
     * @var array
     */
    protected $dates = [

    ];


}
