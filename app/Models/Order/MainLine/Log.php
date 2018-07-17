<?php

namespace App\Models\Order\MainLine;

use App\Models\Model;
use Carbon\Carbon;
use QingLong\UUID\UUID;

/**
 * Class Log
 *
 * @package App\Models\Order\MainLine
 */
class Log extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'order_mainline_logs';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '订单日志';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'order_uuid',
        'driver_uuid',
        'truck_uuid',
        'order_status',
        'type',
        'reg_time',
        'title',
        'description',
        'images',
        'status',
        'remark',
        'current_mileage',
        'current_mileage_image',
        'longitude',
        'latitude',
        'contents',
    ];

    /**
     * 需要转为日期的字段
     *
     * @var array
     */
    protected $dates = [
        'reg_time',
    ];

    /**
     * 应该被转换成原生类型的属性
     *
     * @var array
     */
    protected $casts = [
        'images'   => 'array',
        'contents' => 'array',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Log $orderMainLineLog) {
            //先设置删除标志,等待UUID填充后，清除删除标志
            $orderMainLineLog->setAttribute('delete_time', Carbon::now());
        });

        static::created(function (Log $orderMainLineLog) {
            // 生成订单UUID并填充
            $orderMainLineLogUUID = UUID::buildById(cons('uuid.order_log'), $orderMainLineLog->id);
            $orderMainLineLogData = [
                'order_log_uuid' => $orderMainLineLogUUID,
                'delete_time'    => null,
            ];
            $orderMainLineLog->forceFill($orderMainLineLogData)->save();
        });
    }
}
