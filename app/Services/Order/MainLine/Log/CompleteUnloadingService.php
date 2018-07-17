<?php

namespace App\Services\Order\MainLine\Log;

use App\Services\Order\MainLine\LogService;
use App\Services\Order\MainLine\StatusService;
use Carbon\Carbon;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

class CompleteUnloadingService extends BaseService
{
    /**
     * 卸货完成
     *
     * @param string $orderUUID
     * @param string $driverUUID
     * @param array  $parameters
     *
     * @return \App\Models\Order\MainLine\Log
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function create($orderUUID, $driverUUID, $parameters = [])
    {
        // 检查是否允许操作
        $this->checkActionAllow($orderUUID, 'complete_unloading');

        //检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        //检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        //获取当前卸货地址
        $mainLinePlace = $this->getCurrentUnloadingPlaceByOrderUUID($orderUUID);

        // 验证订单当前状态（arrive_unloading）是否允许切换 到 finish_unloading
        if (!((new StatusService())->canChangeStatus($orderMainLine->order_status, 'finish_unloading'))) {
            throw new Client\ForbiddenException('当前订单状态异常');
        }

        $data = [
            'order_uuid'   => $orderUUID,
            'driver_uuid'  => $driverUUID,
            'truck_uuid'   => $truckUUID,
            'place_uuid'   => $mainLinePlace->place_uuid,
            'full_address' => $mainLinePlace->area_name . $mainLinePlace->address,
        ];
        //记录$parameters
        $description      = '完成卸货：' . $data['full_address'];
        $orderMainLineLog = (new LogService())->logCompleteUnloading($orderMainLine, $driverUUID,
            array_merge($data, $parameters), [
                'description' => $description,
                'images'      => [],
            ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }

        //更新离开卸货地时间
        $mainLinePlacesUpdate = $mainLinePlace->setAttribute('departure_time', Carbon::now())->save();
        if (!$mainLinePlacesUpdate) {
            throw new Server\InternalServerException('更新离开卸货地时间失败');
        }

        //改变订单状态
        (new StatusService())->changeStatus($orderMainLine, 'finish_unloading');

        return $orderMainLineLog;
    }
}