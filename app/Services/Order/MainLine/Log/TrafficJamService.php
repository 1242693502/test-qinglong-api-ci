<?php

namespace App\Services\Order\MainLine\Log;

use App\Services\Order\MainLine\LogService;
use Urland\Exceptions\Server;

class TrafficJamService extends BaseService
{
    /**
     * 记录堵车
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
        /*
         * 1. 检查司机是否正在驾驶某车辆（需要判断is_driving是否为true）
         * 2. 检查正在驾驶的车辆是否能操作该订单
         * 3. 调用LogService，记录$parameters到日志
         * 4. 返回记录的日志
         */

        // 检查是否允许操作
        $this->checkActionAllow($orderUUID, 'traffic_jam');

        // 检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        // 检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        //记录$parameters
        $description = '记录堵车，共包含照片：' . count($parameters['images']) . '张';
        $images      = [];
        foreach ($parameters['images'] as $key => $code) {
            $images[] = [
                'name' => '堵车照片' . ($key + 1),
                'code' => $code,
            ];
        }
        $orderMainLineLog = (new LogService())->logTrafficJam($orderMainLine, $driverUUID, $parameters, [
            'description' => $description,
            'images'      => $images,
        ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }
        return $orderMainLineLog;
    }
}