<?php

namespace App\Services\Order\MainLine;

use App\Models\Driver\DriverTruck;
use App\Models\Order\MainLine;
use App\Models\Order\OrderMainLine;
use App\Models\Truck\Truck;
use App\Services\BaseService;
use App\Services\Truck\TruckStatusService;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

class TruckService extends BaseService
{
    /**
     * 订单指派车辆 (对外可调用)
     *
     * @param $orderUUID
     * @param $truckUUID
     *
     * @return \App\Models\Order\OrderMainLine
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function appointTruck($orderUUID, $truckUUID)
    {
        //TODO
        //涉及表包括
        //1. order_mainlines
        //2. order_mainline_truck
        //3. order_mainline_statuses
        //4. order_mainline_logs
        //5. trucks
        //6. truck_status
        //业务逻辑
        // 1. 记录订单日志 order_mainline_logs
        // 2. 绑定车辆信息 order_mainline_truck
        // 3. 记录订单车辆信息 order_mainlines
        // 4. 订单状态切换状态 到 'driver_confirm'
        // 5. 车辆状态切换 到 'driver_confirm'

        // 检查是否允许操作
        $stage = ActionService::serviceForOrderUUID($orderUUID)->stage();
        if (!$stage || !$stage->action('appoint_truck') || !$stage->action('appoint_truck')->computedAllow()) {
            throw new Client\ForbiddenException('禁止操作');
        }

        // 判断车辆是否存在
        $truck = Truck::where('truck_uuid', $truckUUID)->first();
        if (!$truck) {
            throw Client\ValidationException::withMessages(['truck_uuid' => '车辆不存在']);
        }

        // 判断车辆状态
        if ($truck->truck_status !== cons('truck.status.available')) {
            throw new Client\BadRequestException('当前车辆状态不允许分配订单');
        }

        // 判断车辆有没有主司机
        $driverTruck = DriverTruck::where(['truck_uuid' => $truckUUID, 'is_driving' => true])->first();
        if (empty($driverTruck)) {
            throw new Client\ForbiddenException('当前车辆没有主司机');
        }

        // 获取订单
        $orderMainLine = OrderMainLine::where('order_uuid', $orderUUID)->first();

        // 判断订单是否存在
        if (!$orderMainLine) {
            throw Client\ValidationException::withMessages(['order_uuid' => '订单不存在']);
        }

        // 判断订单状态是否为新创建订单
        if ($orderMainLine->order_status != cons('order.mainline.status.created')) {
            throw new Client\BadRequestException('订单未创建成功或订单已被指派');
        }

        // 判断订单是否已被指派
        if ($orderMainLine->truck_uuid || $orderMainLine->trailer_uuid) {
            throw new Client\BadRequestException('订单已被指派');
        }

        // 验证订单当前状态是否允许切换 到 driver_confirm
        if (!((new StatusService())->canChangeStatus($orderMainLine->order_status, 'driver_confirm'))) {
            throw new Client\ForbiddenException('当前订单状态不允许派单');
        }
        // 验证车辆当前状态是否允许切换 到 driver_confirm
        if (!((new TruckStatusService())->canChangeStatus($truck, 'driver_confirm'))) {
            throw new Client\ForbiddenException('当前车辆状态不允许派单');
        }

        // 记录订单日志
        $contentsParams   = [
            'license_plate_number' => $truck->license_plate_number,
        ];
        $orderMainLineLog = (new LogService())->logAppointTruck($orderMainLine, null, $contentsParams, [
            'truck_uuid'  => $truckUUID,
            'description' => '订单指派车辆，车辆车牌：' . $truck->license_plate_number,
            'images'      => [],
        ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }

        // 当前订单如果未绑定车辆
        if (!MainLine\Truck::where('order_uuid', $orderMainLine->order_uuid)->first(['id'])) {
            // 绑定车辆信息
            $orderMainLineTruckData = [
                'order_uuid'  => $orderMainLine->order_uuid,
                'truck_uuid'  => $truck->truck_uuid,
                'truck_plate' => $truck->license_plate_number,
                'status'      => 1,
                'note'        => '派单',
            ];

            $orderMainLineTruck = MainLine\Truck::create($orderMainLineTruckData);
            if (!$orderMainLineTruck->exists) {
                throw new Server\InternalServerException('绑定车辆信息失败');
            }
        }

        // 记录订单车辆信息
        $orderMainLineUpdateData = [
            'truck_uuid'  => $truck->truck_uuid,
            'truck_plate' => $truck->license_plate_number
        ];

        $orderMainLineUpdate = $orderMainLine->fill($orderMainLineUpdateData)->save();
        if (!$orderMainLineUpdate) {
            throw new Server\InternalServerException('记录订单车辆信息失败');
        }

        // 订单状态切换状态 到 'driver_confirm'
        (new StatusService())->changeStatus($orderMainLine, 'driver_confirm');
        // 车辆状态切换 到 'driver_confirm'
        (new TruckStatusService())->changeStatus($truck, 'driver_confirm');

        return $orderMainLine->fresh();

    }

    /**
     * 获取当前正在运输的订单
     *
     * @param string $truckUUID
     *
     * @return \App\Models\Order\OrderMainLine|null
     */
    public function getCurrentOrder($truckUUID)
    {
        $orderStatus  = cons('order.mainline.status');
        $searchStatus = [
            $orderStatus['driver_prepare'],
            $orderStatus['in_transit'],
            $orderStatus['arrive_loading'],
            $orderStatus['arrive_unloading'],
            $orderStatus['finish_unloading'],
        ];

        $orderMainLine = OrderMainLine::where('truck_uuid', $truckUUID)->whereIn('order_status',
            $searchStatus)->first();
        return $orderMainLine;
    }
}