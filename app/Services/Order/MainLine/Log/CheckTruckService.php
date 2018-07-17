<?php

namespace App\Services\Order\MainLine\Log;

use App\Models\Truck;
use App\Services\Order\MainLine\LogService;
use App\Services\Order\MainLine\StatusService;
use Illuminate\Support\Arr;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

class CheckTruckService extends BaseService
{
    /**
     * 检查车辆证件
     *
     * @param       $orderUUID
     * @param       $driverUUID
     * @param array $parameters
     *
     * @return \App\Models\Order\MainLine\Log
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function checkTruckCertificates($orderUUID, $driverUUID, $parameters = [])
    {
        // 检查是否允许操作
        $this->checkActionAllow($orderUUID, 'check_truck_certs');

        //检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        //检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        $truck = Truck\Truck::where('truck_uuid', $orderMainLine->truck_uuid)->first();
        if (!$truck) {
            throw new Client\NotFoundException('车辆不存在');
        }
        $codes = Arr::get($parameters, 'codes', []);

        $missingCertificates = [];
        $images              = [];
        $description         = '车辆证件检查：' . (empty($codes) ? '证件齐全' : '证件缺失');

        if ($codes) {
            // 缺失证件
            $checkCertificates = app('file-db')->load('truck.check_certificates');
            foreach ($codes as $code) {
                $checkCertificate = $checkCertificates->firstWhere('code', $code);
                if (!$checkCertificate) {
                    throw new Client\BadRequestException('检查证件编号不存在');
                }
                $missingCertificates[] = [
                    'code' => $checkCertificate['code'],
                    'name' => $checkCertificate['name'],
                ];
            }

            // 写入truck_approvals
            $truckApprovalsAttributes = [
                'truck_uuid'  => $orderMainLine->truck_uuid,
                'driver_uuid' => $driverUUID,
                'order_uuid'  => $orderUUID,
                'type'        => cons('truck.approval.type.truck_certificates'),
                'type_name'   => cons()->lang('truck.approval.type.truck_certificates'),
                'description' => $description,
                'images'      => $images,
                'remark'      => $parameters['remark'] ?? null,
                'status'      => cons('truck.approval.status.waiting'),
                'contents'    => [
                    'missing' => $missingCertificates,
                ],
            ];

            $truckApproval = Truck\TruckApproval::create($truckApprovalsAttributes);
            if (!$truckApproval->exists) {
                throw new Server\InternalServerException('记录审批失败');
            }

            // 车辆可用状态字段 is_available 改为 false
            $truck->setAttribute('is_available', false)->save();

        }

        // 记录order_mainline_logs
        $orderMainLineLog = (new LogService())->logCheckTruckCerts($orderMainLine, $driverUUID,
            array_merge($missingCertificates, $parameters), [
                'description' => $description,
                'images'      => [],
            ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }
        return $orderMainLineLog;
    }

    /**
     * 检查车辆
     *
     * @param       $orderUUID
     * @param       $driverUUID
     * @param array $parameters
     *
     * @return \App\Models\Order\MainLine\Log
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function checkTruck($orderUUID, $driverUUID, $parameters = [])
    {
        // 检查是否允许操作
        $this->checkActionAllow($orderUUID, 'check_truck');

        //检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        //检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        // 检查订单状态是否正常
        $orderStatusKey = cons()->key('order.mainline.status', $orderMainLine->order_status);
        switch ($orderStatusKey) {
            case 'driver_prepare':
                // 验证订单当前状态（driver_prepare）是否允许切换 到 in_transit
                if (!((new StatusService())->canChangeStatus($orderMainLine->order_status, 'in_transit'))) {
                    throw new Client\ForbiddenException('当前订单状态异常');
                }
                break;

            case 'arrive_unloading':
                // 卸货阶段允许检查车辆
                break;

            default:
                throw new Client\ForbiddenException('当前订单状态异常');
        }

        $truck = Truck\Truck::where('truck_uuid', $orderMainLine->truck_uuid)->first();
        if (!$truck) {
            throw new Client\NotFoundException('车辆不存在');
        }
        $codes = Arr::get($parameters, 'codes', []);

        $data        = [];
        $images      = [];
        $description = '车辆检查：' . (empty($codes) ? '正常' : '异常') . ' 共包含照片：' . count($parameters['images']) . '张';

        if ($codes) {
            // 存在异常
            $checks = app('file-db')->load('truck.checks');
            foreach ($codes as $code) {
                $check = $checks->firstWhere('code', $code);
                if (!$check) {
                    throw new Client\BadRequestException('异常编号不存在');
                }
                $data[] = [
                    'code' => $check['code'],
                    'name' => $check['name'],
                ];
            }

            // 异常照片
            foreach (Arr::get($parameters, 'images', []) as $key => $imageCode) {
                $images[] = [
                    'name' => '车辆异常照片' . ($key + 1),
                    'code' => $imageCode,
                ];
            }
        }

        // 记录order_mainline_logs
        $orderMainLineLog = (new LogService())->logCheckTruck($orderMainLine, $driverUUID,
            array_merge($data, $parameters), [
                'description' => $description,
                'images'      => $images,
            ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }

        if ($orderStatusKey === 'driver_prepare') {
            // 订单状态切换到 in_transit
            (new StatusService())->changeStatus($orderMainLine, 'in_transit');
        }

        return $orderMainLineLog;


    }

}