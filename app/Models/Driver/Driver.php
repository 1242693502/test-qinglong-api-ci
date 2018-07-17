<?php

namespace App\Models\Driver;

use App\Models\Model;
use App\Models\Truck\Truck;
use Carbon\Carbon;
use QingLong\UUID\UUID;

/**
 * Class Driver
 *
 * @package App\Models\Driver
 *
 * @property-read \App\Models\Truck\Truck $drivingTruck
 */
class Driver extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'drivers';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '司机';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'job_number',
        'phone',
        'phone_is_auth',
        'id_number',
        'id_number_is_auth',
        'driver_type',
        'driver_license_type',
        'qualification',
        'contact_address_code',
        'contact_address_name',
        'contact_address',
        'audit_status',
        'login_status',
        'open_account_time'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'open_account_time'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Driver $driver) {
            //先设置删除标志,等待UUID填充后，清除删除标志
            $driver->setAttribute('delete_time', Carbon::now());
        });

        static::created(function (Driver $driver) {
            // 生成司机UUID并填充
            $driverUUID = UUID::buildById(cons('uuid.driver'), $driver->id);
            $driverData = [
                'driver_uuid' => $driverUUID,
                'delete_time' => null
            ];
            $driver->forceFill($driverData)->save();

            // 默认创建车辆关联
            DriverTruck::create([
                'driver_uuid' => $driver->driver_uuid,
                'is_driving'  => false,
                'truck_uuid'  => null,
            ]);
        });
    }

    /**
     * 当前正在驾驶的车辆
     *
     * @return \QingLong\Eloquent\Relations\BelongsToOne
     */
    public function drivingTruck()
    {
        return $this->belongsToOne(Truck::class, 'driver_truck', 'driver_uuid', 'truck_uuid', 'driver_uuid', 'truck_uuid')
            ->using(DriverTruck::class)->withPivot([
                'driver_uuid',
                'is_driving',
                'truck_uuid',
                'reg_time',
                'remark',
            ]);
    }

    /**
     * 关联证件照
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function certificates()
    {
        return $this->hasMany(DriverCertificate::class, 'driver_uuid', 'driver_uuid');
    }
}