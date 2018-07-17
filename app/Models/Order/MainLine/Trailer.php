<?php

namespace App\Models\Order\MainLine;

use App\Models\Model;

/**
 * Class Trailer
 *
 * @package App\Models\Order\MainLine
 */
class Trailer extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'order_mainline_trailer';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '专线订单挂车关联表';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'order_uuid',
        'trailer_uuid',
        'trailer_plate',
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
