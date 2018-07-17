<?php

namespace App\Models\GasCard;

use App\Models\Model;
use Carbon\Carbon;
use QingLong\UUID\UUID;

/**
 * Class FillingStation
 *
 * @package App\Models\GasCard
 */
class FillingStation extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'filling_stations';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '加油站表';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'area_code',
        'area_name',
        'address',
        'longitude',
        'latitude',
    ];
}