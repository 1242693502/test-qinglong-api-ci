<?php

namespace App\Services\Order\MainLine\Log;

use App\Models\Order\MainLine;
use App\Services\Order\MainLine\LogService;
use App\Services\Order\MainLine\StatusService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

class ArriveUnloadingService extends BaseService
{
    /**
     * 到达装货地
     *
     * @param string $orderUUID
     * @param string $driverUUID
     * @param string $placeUUID
     * @param array  $parameters
     *
     * @return \App\Models\Order\MainLine\Log
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function create($orderUUID, $driverUUID, $placeUUID, $parameters = [])
    {
        // 检查是否允许操作
        $this->checkActionAllow($orderUUID, 'arrive_unloading');

        //检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        //检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        $mainLinePlace = MainLine\Place::where('place_uuid', $placeUUID)->where('type',
            cons('order.mainline.place.type.unloading'))->first(['id', 'area_name', 'address', 'order_uuid']);
        if (!$mainLinePlace) {
            throw new Client\NotFoundException('卸货地址不存在');
        }

        // 判断是否属于同一订单
        if ($mainLinePlace->order_uuid !== $orderUUID) {
            throw new Client\BadRequestException('装货地址不正确');
        }

        // 验证订单是否能够切换状态
        if (!((new StatusService())->canChangeStatus($orderMainLine->order_status, 'arrive_unloading'))) {
            throw new Client\ForbiddenException('当前订单状态切换无效');
        }

        //改变订单状态
        (new StatusService())->changeStatus($orderMainLine, 'arrive_unloading');

        //更新到达时间
        $mainLinePlacesUpdate = $mainLinePlace->setAttribute('arrival_time', Carbon::now())->save();
        if (!$mainLinePlacesUpdate) {
            throw new Server\InternalServerException('更新订单到达时间失败');
        }

        $data = [
            'place_uuid'   => $placeUUID,
            'full_address' => $mainLinePlace->area_name . $mainLinePlace->address,
        ];

        //记录$parameters
        $description = '到达卸货地：' . $data['full_address'];
        $images      = [];

        $orderMainLineLog = (new LogService())->logArriveUnloading($orderMainLine, $driverUUID,
            array_merge($data, $parameters), [
                'description_append_mileage' => true,
                'description'                => $description,
                'images'                     => $images,
            ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }

        return $orderMainLineLog;
    }
}