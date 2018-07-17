<?php

namespace App\Models\Truck;

use App\Models\Model;

/**
 * Class TruckGasCard
 *
 * @package App\Models\Truck
 */
class TruckGasCard extends Model
{
    protected $table = 'truck_gas_cards';

    /**
     * @var string
     */
    const MODEL_NAME = '车辆油卡';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'truck_uuid',
        'gas_card_no',
        'bind_time',
        'channel',
        'status',
        'unbind_time',
        'unbind_reason',
        'loss_time',
        'loss_reason',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'bind_time',
        'unbind_time',
        'loss_time',
    ];
}