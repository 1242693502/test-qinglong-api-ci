<?php

namespace App\Models\Truck;

use App\Models\Model;
use Carbon\Carbon;
use QingLong\UUID\UUID;

/**
 * Class TruckLog
 *
 * @package App\Models\Truck
 */
class TruckLog extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'truck_logs';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '行车日志表';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'truck_uuid',
        'driver_uuid',
        'order_uuid',
        'reg_time',
        'type',
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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'reg_time',
    ];

    /**
     * The attributes that should be cast to native types.
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

        static::creating(function (TruckLog $truckLog) {
            //先设置删除标志,等待UUID填充后，清除删除标志
            $truckLog->setAttribute('delete_time', Carbon::now());
        });

        static::created(function (TruckLog $truckLog) {
            // 生成订单UUID并填充
            $truckLogUUID = UUID::buildById(cons('uuid.truck_log'), $truckLog->id);
            $truckLogData = [
                'truck_log_uuid' => $truckLogUUID,
                'delete_time'    => null,
            ];
            $truckLog->forceFill($truckLogData)->save();
        });
    }
}