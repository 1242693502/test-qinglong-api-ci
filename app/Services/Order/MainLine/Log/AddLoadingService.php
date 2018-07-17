<?php

namespace App\Services\Order\MainLine\Log;

use App\Models\Order\MainLine;
use App\Services\Order\MainLine\LogService;
use App\Services\Order\MainLine\StatusService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use QingLong\Platform\Area\Area;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

class AddLoadingService extends BaseService
{
    /**
     * 添加多点装货地
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
        $this->checkActionAllow($orderUUID, 'add_loading');

        //检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        //检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        // 获取当前装货地址
        $currentLoadingPlace = $this->getCurrentLoadingPlaceByOrderUUID($orderUUID);

        $areaCode            = Arr::pull($parameters, 'area_code');
        $address             = Arr::pull($parameters, 'address');
        $addressContactName  = Arr::pull($parameters, 'address_contact_name');
        $addressContactPhone = Arr::pull($parameters, 'address_contact_phone');

        $areaInfo = app(Area::class)->getFinalInfo($areaCode);
        if (!$areaInfo) {
            throw new Client\NotFoundException('获取地址信息失败');
        }

        // 验证订单是否能够切换状态
        if (!((new StatusService())->canChangeStatus($orderMainLine->order_status, 'in_transit'))) {
            throw new Client\ForbiddenException('当前订单状态切换无效');
        }

        $data = [
            'order_uuid'            => $orderUUID,
            'type'                  => cons('order.mainline.place.type.loading'),
            'address_contact_name'  => $addressContactName,
            'address_contact_phone' => $addressContactPhone,
            'area_code'             => $areaCode,
            'area_name'             => $areaInfo['full_name'],
            'address'               => $address,
        ];

        //记录$parameters
        $description      = '添加装货地：' . $areaInfo['full_name'] . $address . ' 联系人：' . $addressContactName . ' 联系电话：' . $addressContactPhone;
        $orderMainLineLog = (new LogService())->logAddLoading($orderMainLine, $driverUUID,
            array_merge($data, $parameters), [
                'description' => $description,
                'images'      => [],
            ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }

        //记录地点
        $newMainLinePlace = MainLine\Place::create($data);
        if (!$newMainLinePlace->exists) {
            throw new Server\InternalServerException('记录装货地失败');
        }

        //更新离开装货地时间
        $mainLinePlacesUpdate = $currentLoadingPlace->setAttribute('departure_time', Carbon::now())->save();
        if (!$mainLinePlacesUpdate) {
            throw new Server\InternalServerException('更新离开装货地时间失败');
        }

        //改变订单状态
        (new StatusService())->changeStatus($orderMainLine, 'in_transit');

        return $orderMainLineLog;
    }
}