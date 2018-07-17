<?php

namespace App\Services\Order\MainLine\Log;

use App\Models\Trailer\Trailer;
use App\Models\Order\MainLine;
use App\Models\Truck;
use App\Services\Order\MainLine\LogService;
use Illuminate\Support\Arr;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

class CheckTrailerService extends BaseService
{
    /**
     * 挂车证件检查
     *
     * @param       $orderUUID
     * @param       $driverUUID
     * @param array $parameters
     *
     * @return \App\Models\Order\MainLine\Log
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function create($orderUUID, $driverUUID, $parameters = [])
    {
        // 检查是否允许操作
        $this->checkActionAllow($orderUUID, 'check_trailer_certs');

        //检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        //检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        $trailerPlate = $parameters['trailer_plate'];
        $trailer      = Trailer::where('license_plate_number', $trailerPlate)->first([
            'trailer_uuid',
            'license_plate_number',
        ]);
        $trailerUUID  = $trailer ? $trailer->trailer_uuid : null;

        $truck = Truck\Truck::where('truck_uuid', $orderMainLine->truck_uuid)->first();
        if (!$truck) {
            throw new Client\NotFoundException('车辆不存在');
        }
        $codes = Arr::get($parameters, 'codes', []);

        $missingCertificates = [];
        $images              = [];
        $description         = '挂车证件检查：' . (empty($codes) ? '证件齐全' : '证件缺失') . ' 共包含照片：' . count($parameters['images']) . '张 挂车车牌号：' . $trailerPlate;
        foreach ($parameters['images'] as $key => $code) {
            $images[] = [
                'name' => '挂车证件照片' . ($key + 1),
                'code' => $code,
            ];
        }

        if ($codes) {
            // 缺失证件
            $checkCertificates = app('file-db')->load('trailer.check_certificates');
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
                'type'        => cons('truck.approval.type.trailer_certificates'),
                'type_name'   => cons()->lang('truck.approval.type.trailer_certificates'),
                'description' => $description,
                'images'      => $images,
                'remark'      => $parameters['remark'] ?? null,
                'status'      => cons('truck.approval.status.waiting'),
                'contents'    => [
                    'missing' => $missingCertificates,
                ],
            ];
            $truckApproval            = Truck\TruckApproval::create($truckApprovalsAttributes);
            if (!$truckApproval->exists) {
                throw new Server\InternalServerException('记录审批失败');
            }

            // 车辆可用状态字段 is_available 改为 false
            $truck->setAttribute('is_available', false)->save();
        }

        $orderMainLineUpdateData = [
            'trailer_uuid'  => $trailerUUID,
            'trailer_plate' => $trailerPlate,
        ];
        //更新挂车UUID和挂车车牌
        $orderMainLineUpdate = $orderMainLine->fill($orderMainLineUpdateData)->save();
        if (!$orderMainLineUpdate) {
            throw new Server\InternalServerException('更新挂车UUID和挂车车牌失败');
        }

        //记录order_mainline_trailer
        MainLine\Trailer::create([
            'order_uuid'    => $orderUUID,
            'trailer_uuid'  => $trailerUUID,
            'trailer_plate' => $trailerPlate,
            'status'        => 1,
            'note'          => '挂车证件检查',
        ]);

        //记录$parameters
        $trailerCheckCertificates = app('file-db')->load('trailer.check_certificates');
        $trailerCheckCertificates = Arr::pluck($trailerCheckCertificates, 'name', 'code');

        $names = [];
        foreach ($parameters['codes'] as $code) {
            $names[] = $trailerCheckCertificates[$code];
        }
        $parameters['names']        = $names;
        $parameters['trailer_uuid'] = $trailerUUID;

        $orderMainLineLog = (new LogService())->logCheckTrailerCerts($orderMainLine, $driverUUID, $parameters, [
            'description' => $description,
            'images'      => $images,
        ]);
        if (!$orderMainLineLog->exists) {
            throw new Server\InternalServerException('记录订单日志失败');
        }
        return $orderMainLineLog;
    }

    /**
     * 检查挂车
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
    public function checkTrailer($orderUUID, $driverUUID, $parameters = [])
    {
        // 检查是否允许操作
        $this->checkActionAllow($orderUUID, 'check_trailer');

        //检查司机是否驾驶车辆
        $truckUUID = $this->getDrivingTruckByDriverUUID($driverUUID);

        //检查驾驶中的车辆是否能操作该订单
        $orderMainLine = $this->getOrderByOrderUUIDAndTruckUUID($orderUUID, $truckUUID);

        $truck = Truck\Truck::where('truck_uuid', $orderMainLine->truck_uuid)->first();
        if (!$truck) {
            throw new Client\NotFoundException('车辆不存在');
        }
        $codes = Arr::get($parameters, 'codes', []);

        $data        = [];
        $images      = [];
        $description = '挂车检查：' . (empty($codes) ? '正常' : '异常') . ' 共包含照片：' . count($parameters['images']) . '张';

        if ($codes) {
            // 存在异常
            $checks = app('file-db')->load('trailer.checks');
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
                    'name' => '挂车异常照片' . ($key + 1),
                    'code' => $imageCode,
                ];
            }
        }

        // 记录order_mainline_logs
        $orderMainLineLog = (new LogService())->logCheckTrailer($orderMainLine, $driverUUID,
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