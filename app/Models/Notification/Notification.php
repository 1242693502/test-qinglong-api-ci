<?php

namespace App\Models\Notification;

use App\Models\Model;

class Notification extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '通知';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'to_uuid',
        'to_type',
        'to_name',
        'from_uuid',
        'from_type',
        'from_name',
        'type',
        'title',
        'description',
        'status',
        'contents',
    ];

    /**
     * 需要转为日期的字段
     *
     * @var array
     */
    protected $dates = [];

    /**
     * 应该被转换成原生类型的属性
     *
     * @var array
     */
    protected $casts = [
        'contents' => 'array',
    ];
}