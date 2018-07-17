<?php

namespace App\Models\Driver;

use App\Models\Model;

/**
 * Class DriverCertificate
 *
 * @package App\Models\Driver
 */
class DriverCertificate extends Model
{
    protected $table = 'driver_certificates';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'driver_uuid',
        'code',
        'name',
        'image',
        'number',
        'all_field',
    ];
}