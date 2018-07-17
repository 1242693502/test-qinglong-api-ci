<?php

namespace App\Services\Order\MainLine;

use App\Models\Order\MainLine;
use App\Models\Order\OrderMainLine;
use App\Services\BaseService;
use Urland\Exceptions\Server;

/**
 * Class StatusService
 *
 * @package App\Services\Driver
 */
class StatusService extends BaseService
{
    /**
     * 状态转换控制
     *
     * @var array $statusMachine
     */
    const STATUS_MACHINE = [
        'uncreated'        => ['created', 'cancel'],
        'created'          => ['driver_confirm', 'cancel'],
        'driver_confirm'   => ['driver_prepare', 'cancel'],
        'driver_prepare'   => ['in_transit', 'cancel'],
        'in_transit'       => ['arrive_loading', 'arrive_unloading', 'cancel'],
        'arrive_loading'   => ['in_transit', 'cancel'],
        'arrive_unloading' => ['in_transit', 'finish_unloading', 'cancel'],
        'finish_unloading' => ['success', 'cancel'],
        'cancel'           => ['created'],
        'success'          => [],
    ];

    /**
     * 判断某个状态值能否切换到某个状态key
     *
     * @param int    $status
     * @param string $toStatusKey
     *
     * @return bool
     */
    public function canChangeStatus($status, $toStatusKey)
    {
        if (!isset(self::STATUS_MACHINE[$toStatusKey])) {
            return false;
        }

        $statusKey = cons()->key('order.mainline.status', $status);
        if (isset(self::STATUS_MACHINE[$statusKey]) && in_array($toStatusKey, self::STATUS_MACHINE[$statusKey])) {
            return true;
        }

        return false;
    }

    /**
     * 订单状态切换 (不对外曝露)
     *
     * @param \App\Models\Order\OrderMainLine $orderMainLine
     * @param string                          $newOrderStatusKey
     *
     * @return bool
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function changeStatus(OrderMainLine $orderMainLine, $newOrderStatusKey)
    {
        if (!isset(self::STATUS_MACHINE[$newOrderStatusKey])) {
            throw new Server\InternalServerException('订单状态不可用');
        }

        $orderStatusKey = cons()->key('order.mainline.status', $orderMainLine->order_status);
        if (!isset(self::STATUS_MACHINE[$orderStatusKey])) {
            throw new Server\InternalServerException('订单状态异常');
        } elseif (!in_array($newOrderStatusKey, self::STATUS_MACHINE[$orderStatusKey])) {
            throw new Server\InternalServerException('订单状态切换无效');
        }

        $newActionFlag  = 0;
        $newOrderStatus = cons('order.mainline.status.' . $newOrderStatusKey);
        // 查询之前的状态记录
        $oldActionFlag = (int)MainLine\Status::where('order_uuid', $orderMainLine->order_uuid)
            ->where('order_status', $newOrderStatus)
            ->orderByDesc('id')
            ->value('action_flag');
        // 如果存在，则进行单例标记过滤
        if ($oldActionFlag) {
            try {
                $stage         = app('ql.action')->make()->stage($newOrderStatusKey);
                $singletonFlag = $stage ? $stage->getSingletonFlag() : 0;
                $newActionFlag = $oldActionFlag & $singletonFlag;
            } catch (\Throwable $e) {
            }
        }

        // 创建新的订单状态
        $orderMainLineStatus = MainLine\Status::create([
            'order_uuid'   => $orderMainLine->order_uuid,
            'order_status' => $newOrderStatus,
            'action_flag'  => $newActionFlag,
        ]);

        //这里利用了数据库的update原子性来保证该函数的事务性
        //update order_mainlines ... where `id` = {id} and `order_status` = {status}
        $updateCount = OrderMainLine::where('id', $orderMainLine->id)
            ->where('order_status', $orderMainLine->order_status)
            ->update(['order_status' => $newOrderStatus]);
        if ($updateCount < 1) {
            //事务回滚
            $orderMainLineStatus->delete();
            throw new Server\InternalServerException('订单状态切换失败，请查看或稍后再试');
        }

        // 更新MainLine\Driver状态
        $driverStatusMapping = [
            'driver_prepare' => 'normal',
            'success'        => 'success',
            'cancel'         => 'cancel',
        ];
        if (isset($driverStatusMapping[$newOrderStatusKey])) {
            $newStatus = cons('order.mainline.driver.status.' . $driverStatusMapping[$newOrderStatusKey]);
            MainLine\Driver::where('order_uuid', $orderMainLine->order_uuid)->update(['status' => $newStatus]);
        }

        return true;
    }
}