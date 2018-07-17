<?php

namespace App\Models\Order\MainLine;

use App\Models\Model;

/**
 * Class Attribute
 *
 * @package App\Models\Order\MainLine
 */
class Attribute extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'order_mainline_attribute';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '专线订单属性表';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'order_uuid',
        'contract_no',
        'contract_image',
        'receipt_images',
        'receipt_statuses',
        'seal_first_no',
        'seal_first_image',
        'seal_second_no',
        'seal_second_image',
        'seal_last_no',
        'seal_last_image',
    ];

    /**
     * 需要转为日期的字段
     *
     * @var array
     */
    protected $dates = [

    ];

    /**
     * @param $value
     */
    protected function setReceiptImagesAttribute($value){
        $this->attributes['receipt_images'] = implode(',', $value);
    }

    /**
     * @return array
     */
    protected function getReceiptImagesAttribute()
    {
        return explode(',', $this->attributes['receipt_images']);
    }

    /**
     * @param $value
     */
    protected function setReceiptStatusesAttribute($value){
        $this->attributes['receipt_statuses'] = implode(',', $value);
    }

    /**
     * @return array
     */
    protected function getReceiptStatusesAttribute()
    {
        return explode(',', $this->attributes['receipt_statuses']);
    }
}
