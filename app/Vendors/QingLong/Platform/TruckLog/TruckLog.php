<?php

namespace QingLong\Platform\TruckLog;

use Illuminate\Support\Arr;
use Urland\Exceptions\Server\InternalServerException;

/**
 * Class TruckLog
 * 获取行车报告：https://ql-truck-log.muniu56.urland.cn/internal-api/
 *
 * @package QingLong\Platform\TruckLog
 */
class TruckLog
{
    /**
     * area实例
     *
     * @var null
     */
    protected $client = null;

    /**
     * Area constructor.
     *
     * @throws InternalServerException
     */
    public function __construct()
    {
        try {
            $this->client = app('api-client')->service('truck-log-api');
        } catch (\Throwable $e) {
            throw new InternalServerException('连接行车日志服务失败');
        }
    }

    /**
     * 根据车牌号码查询车辆当前里程
     *
     * @param string $truckPlateNumber
     *
     * @return int
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function getMileage($truckPlateNumber)
    {
        try {

            $client = app('api-client')->service('truck-log-api');

            $driveReport = $client->get("trucks/{$truckPlateNumber}/latest-drive-report")->getJson();
            return Arr::get($driveReport, 'start_distance', -1);

        } catch (\Exception $e) {
            throw new InternalServerException('获取车辆里程失败', $e);
        }
    }

}