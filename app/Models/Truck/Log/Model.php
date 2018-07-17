<?php

namespace App\Models\Truck\Log;

use App\Models\Model as BaseModel;
use Carbon\Carbon;

class Model extends BaseModel
{
    /**
     * 需要转为日期的字段
     *
     * @var array
     */
    protected $dates = [
        'reg_time'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'has_invoice' => 'bool',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            // 如果不存在登记时间，默认写入当前登记时间
            if (!$model->getAttribute('reg_time')) {
                $model->setAttribute('reg_time', Carbon::now());
            }

            // 默认状态
            $model->setAttribute('status', cons('truck.log.status.normal'));
        });
    }

    /**
     * @param array $value
     */
    protected function setImagesAttribute($value)
    {
        $this->attributes['images'] = implode(',', $value);
    }

    /**
     * @return array
     */
    protected function getImagesAttribute()
    {
        return explode(',', $this->attributes['images']);
    }
}