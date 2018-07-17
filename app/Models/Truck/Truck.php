<?php

namespace App\Models\Truck;

use App\Models\Model;
use Carbon\Carbon;
use QingLong\UUID\UUID;

/**
 * Class Truck
 *
 * @package App\Models\Truck
 */
class Truck extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'trucks';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '车辆';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'license_plate_number',
        'belong_type',
        'brand',
        'engine_number',
        'axle_number',
        'type_code',
        'type_name',
        'length_code',
        'length_name',
        'vin',
        'owner_name',
        'body_color',
        'approved_tonnage',
        'actual_tonnage',
        'is_available',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Truck $truck) {
            //先设置删除标志,等待UUID填充后，清除删除标志
            $truck->setAttribute('delete_time', Carbon::now());
            // 设置默认状态值
            $truck->setAttribute('truck_status', cons('truck.status.available'));
        });

        static::created(function (Truck $truck) {
            $truckUUID = UUID::buildById(cons('uuid.truck'), $truck->id);
            $truckData = [
                'truck_uuid'  => $truckUUID,
                'delete_time' => null
            ];
            $truck->forceFill($truckData)->save();
        });
    }

    /**
     * 车牌转大写
     *
     * @param $value
     *
     * @return string
     */
    public function setLicensePlateNumberAttribute($value)
    {
        return $this->attributes['license_plate_number'] = strtoupper($value);
    }

    /**
     * 关联证件照
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function certificates()
    {
        return $this->hasMany(TruckCertificate::class, 'truck_uuid', 'truck_uuid');
    }
}