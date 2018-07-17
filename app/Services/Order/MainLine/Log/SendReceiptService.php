<?php

namespace App\Services\Order\MainLine\Log;

use App\Models\Order\MainLine;
use App\Services\Order\MainLine\LogService;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

class SendReceiptService extends BaseService
{
    /**
     * 记录交接单据 - 给
     *
     * @param       $orderUUID
     * @param       $driverUUID
     * @param array $parameters
     *
     * @return \App\Models\Order\MainLine\Log
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
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
         * 4. 记录order_mainline_attribute
         * 5. 返回记录的日志
         */

        // 检查是否允许操作
        $this->checkActionAllow($orderUUID, 'send_receipt');

        //  检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        // 检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        // 获取已提交的单据
        $mainLineAttribute = MainLine\Attribute::where('order_uuid',
            $orderUUID)->whereNotNUll('receipt_images')->whereNotNull('receipt_statuses')->first([
            'id',
            'receipt_images',
            'receipt_statuses'
        ]);
        if (!$mainLineAttribute) {
            throw new Client\BadRequestException('单据不存在');
        }
        $parameters['receipt_statuses'] = $mainLineAttribute->receipt_statuses;
        // 重新提交的单据图片对应状态改为1
        foreach ($mainLineAttribute->receipt_images as $key => $receiptImage) {
            foreach ($parameters['receipt_images'] as $newReceiptImage) {
                if ($receiptImage === $newReceiptImage) {
                    $parameters['receipt_statuses'][$key] = 1;
                }
            }
        }

        //记录$parameters
        $description = '记录交接单据，共包含照片：' . count($parameters['receipt_images']) . '张';
        $images      = [];
        foreach ($parameters['receipt_images'] as $key => $code) {
            $images[] = [
                'name' => '随车单据照片' . ($key + 1),
                'code' => $code,
            ];
        }
        $orderMainLineLog = (new LogService())->logSendReceipt($orderMainLine, $driverUUID, $parameters, [
            'description' => $description,
            'images'      => $images,
        ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }

        // 更新记录order_mainline_attribute
        $mainLineAttribute->fill(['receipt_statuses' => $parameters['receipt_statuses']])->save();

        return $orderMainLineLog;
    }
}