<?php

namespace App\Services\Order\MainLine\Log;

use App\Models\Order\MainLine;
use App\Services\Order\MainLine\LogService;
use Urland\Exceptions\Server;

class ReceiveReceiptService extends BaseService
{
    /**
     * 记录交接单据 - 收
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
         * 4. 记录order_mainline_attribute，需要判断是否已对应存在记录，存在则更新，反之插入
         * 5. 返回记录的日志
         */

        // 检查是否允许操作
        $this->checkActionAllow($orderUUID, 'receive_receipt');

        // 检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        // 检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        // 如果存在单据，则单据图片依次加入
        $mainLineAttribute = $mainLineAttribute = MainLine\Attribute::where('order_uuid', $orderUUID)
            ->whereNotNUll('receipt_images')
            ->whereNotNull('receipt_statuses')
            ->first(['id', 'receipt_images']);
        if ($mainLineAttribute) {
            foreach ($mainLineAttribute->receipt_images as $receiptImage) {
                if (!in_array($receiptImage, $parameters['receipt_images'])) {
                    array_push($parameters['receipt_images'], $receiptImage);
                }
            }
        }

        // 记录$parameters
        $receiptStatuses                = array_fill(0, count($parameters['receipt_images']), 0);
        $parameters['receipt_statuses'] = $receiptStatuses;

        $description = '提交交接单据，合同编号：' . $parameters['contract_no'] . ' 共包含照片：' . (count($parameters['receipt_images']) + 1) . '张';
        $images      = [
            ['name' => '合同照片', 'code' => $parameters['contract_image']],
        ];
        foreach ($parameters['receipt_images'] as $key => $code) {
            $images[] = [
                'name' => '随车单据照片' . ($key + 1),
                'code' => $code,
            ];
        }
        $orderMainLineLog = (new LogService())->logReceiveReceipt($orderMainLine, $driverUUID, $parameters, [
            'description' => $description,
            'images'      => $images,
        ]);

        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }

        // 更新或新增order_mainline_attribute
        if ($mainLineAttribute) {
            $mainLineAttribute->fill([
                'receipt_images'   => $parameters['receipt_images'],
                'receipt_statuses' => $parameters['receipt_statuses'],
            ])->save();
        } else {
            MainLine\Attribute::create([
                'order_uuid'       => $orderUUID,
                'contract_no'      => $parameters['contract_no'],
                'contract_image'   => $parameters['contract_image'],
                'receipt_images'   => $parameters['receipt_images'],
                'receipt_statuses' => $receiptStatuses,
            ]);
        }

        return $orderMainLineLog;
    }

}