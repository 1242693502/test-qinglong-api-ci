<?php

namespace App\Services\Order\MainLine\Log;

use App\Models\Order\MainLine;
use App\Services\Order\MainLine\LogService;
use App\Services\Order\MainLine\StatusService;
use App\Services\Truck\TruckStatusService;
use Carbon\Carbon;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

class CompleteService extends BaseService
{
    /**
     * 记录运输完成
     *
     * @param       $orderUUID
     * @param       $driverUUID
     * @param array $parameters
     *
     * @return \App\Models\Order\MainLine\Log
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function create($orderUUID, $driverUUID, $parameters = [])
    {
        // 检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        // 检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        // 车辆验证
        $truck = $orderMainLine->orderTruck;
        if (!$truck) {
            throw new Client\BadRequestException('订单指定车辆不存在');
        }

        // 验证订单当前状态（finish_unloading）是否允许切换 到 success
        if (!((new StatusService())->canChangeStatus($orderMainLine->order_status, 'success'))) {
            throw new Client\ForbiddenException('当前订单状态异常');
        }

        // 验证车辆当前状态是否允许切换 到 available
        if (!((new TruckStatusService())->canChangeStatus($truck, 'available'))) {
            throw new Client\ForbiddenException('当前车辆状态异常');
        }

        // 记录订单日志
        $orderMainLineLog = (new LogService())->logSuccess($orderMainLine, $driverUUID, $parameters, [
            'description' => '记录运输完成',
            'images'      => [],
        ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }

        // 改变订单状态
        (new StatusService())->changeStatus($orderMainLine, 'success');
        // 改变车辆状态
        (new TruckStatusService())->changeStatus($truck, 'available');

        // 完成时间
        $orderMainLine->setAttribute('complete_time', Carbon::now())->save();
        MainLine\Driver::where('order_uuid', $orderUUID)->update(['complete_time' => Carbon::now()]);

        return $orderMainLineLog;
    }

}