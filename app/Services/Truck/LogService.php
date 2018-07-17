<?php

namespace App\Services\Truck;

use App\Models\Truck\TruckLog;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use QingLong\Platform\TruckLog\TruckLog as TruckLogPlatform;
use Urland\Exceptions\Client;

/**
 * Class LogService
 *
 * @package App\Services\Truck
 *
 * @method \App\Models\Truck\TruckLog logRefuel($driverUUID, $truckUUID, array $data, array $processedData = []),
 * @method \App\Models\Truck\TruckLog logCoolant($driverUUID, $truckUUID, array $data, array $processedData = []),
 * @method \App\Models\Truck\TruckLog logPark($driverUUID, $truckUUID, array $data, array $processedData = []),
 * @method \App\Models\Truck\TruckLog logWeight($driverUUID, $truckUUID, array $data, array $processedData = []),
 * @method \App\Models\Truck\TruckLog logRepair($driverUUID, $truckUUID, array $data, array $processedData = []),
 * @method \App\Models\Truck\TruckLog logAdblue($driverUUID, $truckUUID, array $data, array $processedData = []),
 * @method \App\Models\Truck\TruckLog logPenalty($driverUUID, $truckUUID, array $data, array $processedData = []),
 * @method \App\Models\Truck\TruckLog logTollRoad($driverUUID, $truckUUID, array $data, array $processedData = []),
 * @method \App\Models\Truck\TruckLog logOther($driverUUID, $truckUUID, array $data, array $processedData = []),
 */
class LogService extends BaseService
{
    /**
     * 车辆日志统一处理
     *
     * @param string $typeKey
     * @param string $driverUUID
     * @param string $truckUUID
     * @param array  $data
     * @param array  $processedData
     *
     * @return \App\Models\Truck\TruckLog
     * @throws \InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    protected function create(
        $typeKey,
        $driverUUID,
        $truckUUID,
        array $data,
        array $processedData = []
    ) {
        $type = cons('truck.log.type.' . $typeKey);
        if (is_null($type)) {
            throw new Client\BadRequestException('日志类型无效');
        }
        $orderUUID           = Arr::pull($data, 'order_uuid');
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
            $truckService = new TruckService();
            if ($truck = $truckService->getInfoByUUID($truckUUID, ['license_plate_number'])) {
                $currentMileage = app(TruckLogPlatform::class)->getMileage($truck->license_plate_number);
            }
        }

        // 是否需要显示当前里程到日志中
        if ($descriptionAppendMileage) {
            $displayMileage = $currentMileage > 0 ? $currentMileage : 0;
            $description    .= ' 当前里程：' . ($displayMileage / 1000) . '公里';
        }

        $attributes = [
            'order_uuid'            => $orderUUID,
            'driver_uuid'           => $driverUUID,
            'truck_uuid'            => $truckUUID,
            'reg_time'              => Carbon::now(),
            'type'                  => $type,
            // 主标题
            'title'                 => cons()->lang('truck.log.type.' . $typeKey),
            // 副标题
            'description'           => $description,
            // 图片
            'images'                => $images,
            'status'                => true,
            'remark'                => $remark,
            'current_mileage'       => $currentMileage,
            'current_mileage_image' => $currentMileageImage,
            'longitude'             => $longitude,
            'latitude'              => $latitude,
            'contents'              => $data,
        ];

        return TruckLog::create($attributes);
    }

    /**
     * 打印快速访问方法文档
     *
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function printQuickAccessDocs()
    {
        $typeList = cons('truck.log.type');
        $docs     = [];

        foreach ($typeList as $actionCode => $type) {

            $methodName = studly_case($actionCode);
            $docs[]     = "@method \App\Models\Truck\TruckLog log{$methodName}(\$driverUUID, \$truckUUID, array \$data, array \$processedData = [])";
        }

        return $docs;
    }

    /**
     * 动态调用方法
     *
     * @param string $method
     * @param array  $args
     *
     * @return \App\Models\Truck\TruckLog
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     * @throws \Urland\Exceptions\Client\BadRequestException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function __call($method, $args)
    {
        // 记录到日志
        if (starts_with($method, 'log') && count($args) >= 2) {
            $logKey = snake_case(substr($method, 3));
            return $this->create($logKey, $args[0], $args[1], $args[2], Arr::get($args, 3, []));
        }

        throw new \BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $method . '()');
    }
}