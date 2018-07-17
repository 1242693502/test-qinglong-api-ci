<?php

namespace App\Services\Order\MainLine\Log;

use App\Services\Order\MainLine\LogService;
use Urland\Exceptions\Server;

class UnloadingAbnormalService extends BaseService
{
    /**
     * 记录卸货异常
     *
     * @param       $orderUUID
     * @param       $driverUUID
     * @param array $parameters
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
        $this->checkActionAllow($orderUUID, 'unloading_abnormal');

        // 检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        // 检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        // 获取当前卸货地地址
        $mainLinePlace = $this->getCurrentUnloadingPlaceByOrderUUID($orderUUID);

        $data = [
            'place_uuid'   => $mainLinePlace->place_uuid,
            'full_address' => $mainLinePlace->area_name . $mainLinePlace->address,
        ];

        //记录$parameters
        $description = '卸货异常，卸货地：' . $data['full_address']  . ' 共包含照片：' . count($parameters['images']) . '张' . ' 异常描述：' . $parameters['description'];
        $images      = [];
        foreach ($parameters['images'] as $key => $code) {
            $images[] = [
                'name' => '卸货异常照片' . ($key + 1),
                'code' => $code,
            ];
        }

        $orderMainLineLog = (new LogService())->logUnloadingAbnormal($orderMainLine, $driverUUID,
            array_merge($data, $parameters), [
                'description' => $description,
                'images'      => $images,
            ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }

        return $orderMainLineLog;
    }

}