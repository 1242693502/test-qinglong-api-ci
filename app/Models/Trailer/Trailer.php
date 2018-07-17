<?php

namespace App\Models\Trailer;

use App\Models\Model;
use Carbon\Carbon;
use QingLong\UUID\UUID;

/**
 * Class Trailer
 *
 * @package App\Models\Trailer
 */
class Trailer extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'trailers';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '挂车';

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
        'actual_tonnage'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Trailer $trailer) {
            $trailer->setAttribute('delete_time', Carbon::now());
        });

        static::created(function (Trailer $trailer) {
            $trailerUUID = UUID::buildById(cons('uuid.trailer'), $trailer->id);
            $trailerData = [
                'trailer_uuid' => $trailerUUID,
                'delete_time'  => null
            ];
            $trailer->forceFill($trailerData)->save();
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

}
