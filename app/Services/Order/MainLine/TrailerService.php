<?php

namespace App\Services\Order\MainLine;


use App\Models\Order\MainLine;
use App\Models\Order\OrderMainLine;
use App\Models\Trailer\Trailer;
use App\Services\BaseService;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

class TrailerService extends BaseService
{
    /**
     * 订单指派挂车 (对外可调用)
     *
     * @param string $orderUUID
     * @param string $trailerUUID
     *
     * @return \App\Models\Order\OrderMainLine
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function appointTrailer($orderUUID, $trailerUUID)
    {
        //TODO
        //涉及表包括
        //1. order_mainlines
        //2. order_mainline_trailer
        //3. order_mainline_statuses
        //4. order_mainline_logs
        //业务逻辑
        // 1. 记录订单日志 order_mainline_logs appointTrailer
        // 2. 绑定挂车信息 order_mainline_trailer
        // 3. 记录订单车辆信息 order_mainlines

        // 检查是否允许操作
        $stage = ActionService::serviceForOrderUUID($orderUUID)->stage();
        if (!$stage || !$stage->action('appoint_trailer') || !$stage->action('appoint_trailer')->computedAllow()) {
            throw new Client\ForbiddenException('禁止操作');
        }

        // 判断挂车是否存在
        $trailer = Trailer::where('trailer_uuid', $trailerUUID)->first();
        if (!$trailer) {
            throw Client\ValidationException::withMessages(['trailer_uuid' => '车辆不存在']);
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

        // 记录订单日志
        $contentsParams   = [
            'license_plate_number' => $trailer->license_plate_number,
        ];
        $orderMainLineLog = (new LogService())->logAppointTrailer($orderMainLine, null, $contentsParams, [
            'description' => '订单指派挂车，挂车车牌：' . $trailer->license_plate_number,
            'images'      => [],
        ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }

        // 当前订单如果未绑定挂车
        if (!MainLine\Trailer::where('order_uuid', $orderMainLine->order_uuid)->first(['id'])) {
            // 绑定挂车信息
            $orderMainLineTrailerData = [
                'order_uuid'   => $orderMainLine->order_uuid,
                'trailer_uuid' => $trailer->trailer_uuid,
            ];

            $orderMainLineTrailer = MainLine\Trailer::create($orderMainLineTrailerData);
            if (!$orderMainLineTrailer->exists) {
                throw new Server\InternalServerException('绑定车辆信息失败');
            }
        }

        // 记录订单车辆信息
        $orderMainLineUpdateData = [
            'trailer_uuid'  => $trailer->trailer_uuid,
            'trailer_plate' => $trailer->license_plate_number
        ];

        $orderMainLineUpdate = $orderMainLine->fill($orderMainLineUpdateData)->save();
        if (!$orderMainLineUpdate) {
            throw new Server\InternalServerException('记录订单车辆信息失败');
        }

        return $orderMainLine->fresh();

    }
}