<?php

namespace App\Models\Trailer;

use App\Models\Model;

class TrailerCertificate extends Model
{
    protected $table = 'trailer_certificates';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'trailer_uuid',
        'code',
        'name',
        'image',
        'number',
        'all_field',
    ];
}