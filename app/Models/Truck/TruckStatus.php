<?php

namespace App\Models\Truck;

use App\Models\Model;

/**
 * Class TruckStatus
 *
 * @package App\Models\Truck
 */
class TruckStatus extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'truck_statuses';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '车辆状态';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'truck_uuid',
        'truck_status',
        'note',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
    ];
}