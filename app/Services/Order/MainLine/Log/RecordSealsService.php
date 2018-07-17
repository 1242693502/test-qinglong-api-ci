<?php

namespace App\Services\Order\MainLine\Log;

use App\Models\Order\MainLine;
use App\Services\Order\MainLine\LogService;
use Urland\Exceptions\Server;

class RecordSealsService extends BaseService
{

    /**
     * 录封签号
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
    public function create($orderUUID, $driverUUID, array $parameters = [])
    {
        /*
         * 1. 检查司机是否正在驾驶某车辆（需要判断is_driving是否为true）
         * 2. 检查正在驾驶的车辆是否能操作该订单
         * 3. 调用LogService，记录$parameters到日志
         * 4. 记录order_mainline_attribute
         * 5. 返回记录的日志
         */

        // 检查是否允许操作
        $this->checkActionAllow($orderUUID, 'record_seals');

        // 检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        // 检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        // 记录$parameters
        $description      = '录封签号，共包含照片：3张';
        $images           = [
            ['name' => '封签号边门1', 'code' => $parameters['seal_first_image']],
            ['name' => '封签号边门2', 'code' => $parameters['seal_second_image']],
            ['name' => '封签号尾门', 'code' => $parameters['seal_last_image']],
        ];
        $orderMainLineLog = (new LogService())->logRecordSeals($orderMainLine, $driverUUID, $parameters, [
            'description' => $description,
            'images'      => $images,
        ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }

        // 记录order_mainline_attribute
        MainLine\Attribute::create([
            'order_uuid'        => $orderUUID,
            'seal_first_no'     => $parameters['seal_first_no'],
            'seal_first_image'  => $parameters['seal_first_image'],
            'seal_second_no'    => $parameters['seal_second_no'],
            'seal_second_image' => $parameters['seal_second_image'],
            'seal_last_no'      => $parameters['seal_last_no'],
            'seal_last_image'   => $parameters['seal_last_image'],
        ]);

        return $orderMainLineLog;
    }
}