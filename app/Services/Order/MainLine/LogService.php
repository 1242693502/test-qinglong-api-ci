<?php

namespace App\Services\Order\MainLine;

use App\Models\Order\MainLine\Log;
use App\Models\Order\OrderMainLine;
use App\Services\BaseService;
use App\Services\Truck\TruckService as TruckTruckService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use QingLong\Platform\TruckLog\TruckLog;

/**
 * Class LogService
 *
 * @package App\Services\Driver
 *
 * @method \App\Models\Order\MainLine\Log logAppointTruck(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logAppointTrailer(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logDriverConfirm(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logSwapDriving(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logCheckTruckCerts(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logCheckTruck(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logCheckTrailerCerts(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logCheckTrailer(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logTrafficJam(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logHighWayEnter(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logHighWayLeave(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logArriveLoading(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logReceiveReceipt(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logCountLoadingBegin(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logCountLoadingEnd(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logRecordSeals(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logAddLoading(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logCompleteLoading(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logRecordWeight(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logArriveUnloading(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logSendReceipt(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logCountUnloadingBegin(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logCountUnloadingEnd(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logUnloadingAbnormal(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logAddUnloading(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logCompleteUnloading(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logCancel(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 * @method \App\Models\Order\MainLine\Log logSuccess(\App\Models\Order\OrderMainLine $orderMainLine, $driverUUID, array $data, array $processedData = [])
 */
class LogService extends BaseService
{
    /**
     * 创建一个日志
     *
     * @param \App\Models\Order\OrderMainLine $orderMainLine
     * @param string                          $typeKey
     * @param string|null                     $driverUUID
     * @param array                           $data
     * @param array                           $processedData
     *
     * @return \App\Models\Order\MainLine\Log
     * @throws \InvalidArgumentException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    protected function createByOrder(
        OrderMainLine $orderMainLine,
        $typeKey,
        $driverUUID,
        array $data,
        array $processedData = []
    ) {
        $remark              = Arr::pull($data, 'remark');
        $currentMileage      = Arr::pull($data, 'current_mileage');
        $currentMileageImage = Arr::pull($data, 'current_mileage_image');
        $longitude           = Arr::pull($data, 'longitude');
        $latitude            = Arr::pull($data, 'latitude');

        $descriptionAppendMileage = Arr::pull($processedData, 'description_append_mileage');
        $description              = Arr::get($processedData, 'description');
        $images                   = Arr::get($processedData, 'images', []);

        // 如果里程传null进来，那么再去获取一下当前里程
        if (is_null($currentMileage)) {
            $truckService = new TruckTruckService();
            if ($truck = $truckService->getInfoByUUID($orderMainLine->truck_uuid, ['license_plate_number'])) {
                $currentMileage = app(TruckLog::class)->getMileage($truck->license_plate_number);
            }
        }

        // 是否需要显示当前里程到日志中
        if ($descriptionAppendMileage) {
            $displayMileage = $currentMileage > 0 ? $currentMileage : 0;
            $description    .= ' 当前里程：' . ($displayMileage / 1000) . '公里';
        }

        $attributes = [
            'order_uuid'   => $orderMainLine->order_uuid,
            'driver_uuid'  => $driverUUID,
            'truck_uuid'   => $orderMainLine->truck_uuid ?: Arr::get($processedData, 'truck_uuid'),
            'order_status' => $orderMainLine->order_status,
            'reg_time'     => Carbon::now(),
            'type'         => cons('order.mainline.log.type.' . $typeKey),
            // 主标题
            'title'        => cons()->lang('order.mainline.log.type.' . $typeKey),
            // 副标题
            'description'  => $description,
            // 图片
            'images'       => $images,

            'status'                => true,
            'remark'                => $remark,
            'current_mileage'       => $currentMileage,
            'current_mileage_image' => $currentMileageImage,
            'longitude'             => $longitude,
            'latitude'              => $latitude,
            'contents'              => $data,
        ];

        $orderMainLineLog = Log::create($attributes);

        // 设置操作已经完成
        try {
            ActionService::serviceForOrderUUID($orderMainLine->order_uuid)->setActionsDone($typeKey);
        } catch (\Throwable $e) {
        }

        return $orderMainLineLog;
    }

    /**
     * 打印快速访问方法文档
     *
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function printQuickAccessDocs()
    {
        // TODO: 已失效，需更新代码
        $docs = [];

        foreach ((new ActionService())->getActionObjects() as $actionCode => $actionObject) {
            if (!$actionObject['is_log']) {
                continue;
            }

            $methodName = studly_case($actionCode);
            $docs[]     = "@method \App\Models\Order\MainLine\Log log{$methodName}(\App\Models\Order\OrderMainLine \$orderMainLine, \$driverUUID, array \$data, array \$processedData = [])";
        }

        return $docs;
    }

    /**
     * 动态调用方法
     *
     * @param string $method
     * @param array  $args
     *
     * @return \App\Models\Order\MainLine\Log
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function __call($method, $args)
    {
        // 记录到日志
        if (starts_with($method, 'log') && count($args) >= 2) {
            $logKey = snake_case(substr($method, 3));
            return $this->createByOrder($args[0], $logKey, $args[1], $args[2], Arr::get($args, 3, []));
        }

        throw new \BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $method . '()');
    }
}