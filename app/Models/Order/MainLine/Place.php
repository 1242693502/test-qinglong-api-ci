<?php

namespace App\Models\Order\MainLine;

use App\Models\Model;
use Carbon\Carbon;
use QingLong\UUID\UUID;

/**
 * Class Place
 *
 * @package App\Models\Order\MainLine
 */
class Place extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'order_mainline_places';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '专线订单装卸货地点';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'order_uuid',
        'type',
        'address_contact_name',
        'address_contact_phone',
        'area_code',
        'area_name',
        'address',
    ];

    /**
     * 需要转为日期的字段
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

        static::creating(function (Place $orderMainLinePlace) {
            //先设置删除标志,等待UUID填充后，清除删除标志
            $orderMainLinePlace->setAttribute('delete_time', Carbon::now());
        });

        static::created(function (Place $orderMainLinePlace) {
            // 生成订单UUID并填充
            $orderMainLinePlaceUUID = UUID::buildById(cons('uuid.place'), $orderMainLinePlace->id);
            $orderMainLinePlaceData = [
                'place_uuid'  => $orderMainLinePlaceUUID,
                'delete_time' => null,
            ];
            $orderMainLinePlace->forceFill($orderMainLinePlaceData)->save();
        });
    }
}
