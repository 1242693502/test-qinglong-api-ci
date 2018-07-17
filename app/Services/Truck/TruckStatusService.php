<?php

namespace App\Services\Truck;

use App\Models\Truck\Truck;
use App\Models\Truck\TruckStatus;
use App\Services\BaseService;
use Urland\Exceptions\Server;

class TruckStatusService extends BaseService
{
    /**
     * 状态转换控制
     *
     * @var array $statusMachine
     */
    const STATUS_MACHINE = [
        'available'      => ['driver_confirm'],
        'driver_confirm' => ['in_transit', 'available'],
        'in_transit'     => ['available'],
    ];

    /**
     * 判断车辆能否切换状态
     *
     * @param \App\Models\Truck\Truck $truck
     * @param string                  $toStatusKey
     *
     * @return bool
     */
    public function canChangeStatus(Truck $truck, $toStatusKey)
    {
        if (!$truck->is_available) {
            return false;
        }

        if (!isset(self::STATUS_MACHINE[$toStatusKey])) {
            return false;
        }

        $statusKey = cons()->key('truck.status', $truck->truck_status);
        if (isset(self::STATUS_MACHINE[$statusKey]) && in_array($toStatusKey, self::STATUS_MACHINE[$statusKey])) {
            return true;
        }

        return false;
    }

    /**
     * 更改车辆状态
     *
     * @param \App\Models\Truck\Truck $truck
     * @param string                  $newStatusKey
     * @param null|string             $note
     *
     * @return bool
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function changeStatus(Truck $truck, $newStatusKey, $note = null)
    {
        if (!$truck->is_available) {
            throw new Server\InternalServerException('车辆状态不可用');
        }

        if (!isset(self::STATUS_MACHINE[$newStatusKey])) {
            throw new Server\InternalServerException('车辆状态不可用');
        }

        $truckStatusKey = cons()->key('truck.status', $truck->truck_status);
        if (!isset(self::STATUS_MACHINE[$truckStatusKey])) {
            throw new Server\InternalServerException('车辆状态异常');
        } elseif (!in_array($newStatusKey, self::STATUS_MACHINE[$truckStatusKey])) {
            throw new Server\InternalServerException('车辆状态切换无效');
        }

        $truckStatus = TruckStatus::create([
            'truck_uuid'   => $truck->truck_uuid,
            'truck_status' => cons('truck.status.' . $newStatusKey),
            'note'         => $note,
        ]);

        //这里利用了数据库的update原子性来保证该函数的事务性
        //update trucks ... where `id` = {id} and `truck_status` = {status}
        $updateCount = Truck::where('id', $truck->id)->where('truck_status',
            $truck->truck_status)->update(['truck_status' => cons('truck.status.' . $newStatusKey)]);
        if ($updateCount < 1) {
            //事务回滚
            $truckStatus->delete();
            throw new Server\InternalServerException('订单状态切换失败，请查看或稍后再试');
        }

        return true;
    }
}