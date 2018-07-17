<?php

namespace App\Services\Order\MainLine\Log;

use App\Services\Order\MainLine\LogService;
use App\Services\Order\MainLine\StatusService;
use Carbon\Carbon;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

class CompleteLoadingService extends BaseService
{
    /**
     * 装货完成
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
        $this->checkActionAllow($orderUUID, 'complete_loading');

        //检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        //检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        //获取当前装货地址
        $mainLinePlace = $this->getCurrentLoadingPlaceByOrderUUID($orderUUID);

        // 验证订单是否能够切换状态
        if(!((new StatusService())->canChangeStatus($orderMainLine->order_status, 'in_transit'))){
            throw new Client\ForbiddenException('当前订单状态切换无效');
        }

        $data = [
            'place_uuid'   => $mainLinePlace->place_uuid,
            'full_address' => $mainLinePlace->area_name . $mainLinePlace->address,
        ];
        //记录$parameters
        $description      = '完成装货：' . $data['full_address'];
        $orderMainLineLog = (new LogService())->logCompleteLoading($orderMainLine, $driverUUID,
            array_merge($data, $parameters), [
                'description' => $description,
                'images'      => [],
            ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }

        //更新离开装货地时间
        $mainLinePlacesUpdate = $mainLinePlace->setAttribute('departure_time', Carbon::now())->save();
        if (!$mainLinePlacesUpdate) {
            throw new Server\InternalServerException('更新离开装货的时间失败');
        }

        //改变订单状态
        (new StatusService())->changeStatus($orderMainLine, 'in_transit');

        return $orderMainLineLog;
    }
}