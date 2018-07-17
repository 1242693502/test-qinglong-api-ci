<?php

namespace App\Models\Truck;

use App\Models\Model;

/**
 * Class TruckCertificate
 *
 * @package App\Models\Truck
 */
class TruckCertificate extends Model
{
    protected $table = 'truck_certificates';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'truck_uuid',
        'code',
        'name',
        'image',
        'number',
        'all_field',
    ];
}