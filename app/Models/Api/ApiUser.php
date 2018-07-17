<?php

namespace App\Models\Api;

use App\Models\Model;

class ApiUser extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'api_users';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '接口调用方';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'api_secret',
    ];
}
