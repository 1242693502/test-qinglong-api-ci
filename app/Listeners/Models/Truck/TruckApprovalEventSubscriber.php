<?php

namespace App\Listeners\Models\Truck;

use App\Events\Models\Truck\TruckApprovalCreated;
use App\Events\Models\Truck\TruckApprovalUpdated;
use App\Models\Notification\Notification;

class TruckApprovalEventSubscriber
{
    /**
     * 审核创建事件
     *
     * @param TruckApprovalCreated $event
     */
    public function onCreated($event)
    {
        $truckApproval = $event->truckApproval;
        if ($truckApproval->status !== cons('truck.approval.status.waiting')) {
            return;
        }

        // 创建车辆审核后，添加通知
        Notification::create([
            'to_uuid'     => null,
            'to_type'     => cons('notification.type.admin'),
            'to_name'     => '所有管理员',
            'from_uuid'   => $truckApproval->driver_uuid,
            'from_type'   => cons('notification.type.driver'),
            'from_name'   => $truckApproval->driver->name,
            'type'        => 1,
            'title'       => $truckApproval->type_name,
            'description' => $truckApproval->description,
            'status'      => cons('notification.status.normal'),
            'contents'    => [
                'truck_approval_id' => $truckApproval->id,
            ],
        ]);
    }

    public function onUpdated($event)
    {
        // 更新审核状态
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $currentClass = get_class();

        $events->listen(TruckApprovalCreated::class, $currentClass . '@onCreated');
        $events->listen(TruckApprovalUpdated::class, $currentClass . '@onUpdated');
    }

}