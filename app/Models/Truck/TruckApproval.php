<?php

namespace App\Models\Truck;

use App\Events\Models\Truck\TruckApprovalCreated;
use App\Events\Models\Truck\TruckApprovalUpdated;
use App\Models\Driver\Driver;
use App\Models\Model;

class TruckApproval extends Model
{
    /**
     * 表名
     *
     * @var string
     */
    protected $table = 'truck_approvals';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '车辆审批';

    /**
     * 可以被批量赋值的字段
     *
     * @var array
     */
    protected $fillable = [
        'truck_uuid',
        'driver_uuid',
        'order_uuid',
        'type',
        'type_name',
        'description',
        'images',
        'remark',
        'status',
        'contents',
        'approver_uuid',
        'approver_name',
        'approver_time',
        'approver_reason',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'approver_time',
    ];

    /**
     * 应该被转换成原生类型的属性
     *
     * @var array
     */
    protected $casts = [
        'images'   => 'array',
        'contents' => 'array',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => TruckApprovalCreated::class,
        'updated' => TruckApprovalUpdated::class,
    ];

    /**
     * 关联一个司机
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function driver()
    {
        return $this->hasOne(Driver::class, 'driver_uuid', 'driver_uuid');
    }
}