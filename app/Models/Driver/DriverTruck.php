<?php

namespace App\Models\Driver;

use App\Models\Pivot;
use App\Models\Truck\Truck;
use Carbon\Carbon;

/**
 * Class DriverTruck
 *
 * @package App\Models\Driver
 */
class DriverTruck extends Pivot
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'driver_truck';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '司机车辆关联';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'reg_time'
    ];

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'driver_uuid',
        'is_driving',
        'truck_uuid',
        'reg_time',
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

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (DriverTruck $driverTruck) {
            // 如果不存在登记时间，默认写入当前登记时间
            if (!$driverTruck->getAttribute('reg_time')) {
                $driverTruck->setAttribute('reg_time', Carbon::now());
            }
        });
    }

    /**
     * 关联一个司机
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function driver()
    {
        return $this->hasOne(Driver::class, 'driver_uuid', 'driver_uuid');
    }

    /**
     * 关联一辆车
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function truck()
    {
        return $this->hasOne(Truck::class, 'truck_uuid', 'truck_uuid');
    }
}