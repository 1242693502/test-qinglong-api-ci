<?php

namespace App\Services\GasCard;

use App\Models\GasCard\FillingStation;
use App\Services\BaseService;
use QingLong\Platform\Area\Area;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

/**
 * Class FillingStationService
 *
 * @package App\Services\GasCard
 */
class FillingStationService extends BaseService
{
    /**
     * 通过UUID获取加油站信息
     *
     * @param string $stationUUID
     * @param array  $fields
     *
     * @return mixed
     */
    public function getInfoByUUID($stationUUID, $fields = [])
    {
        return FillingStation::where('station_uuid', $stationUUID)->first($fields);
    }

    /**
     * 创建加油站
     *
     * @param array $stationData
     *
     * @return mixed
     * @throws \Urland\Exceptions\Client\ValidationException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function create($stationData)
    {
        $areaInfo                 = app(Area::class)->getInfo($stationData['area_code']);
        $stationData['area_name'] = $areaInfo['full_name'];

        return FillingStation::create($stationData);
    }

    /**
     * 更新加油站
     *
     * @param $stationData
     * @param $stationID
     *
     * @return mixed
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function update($stationData, $stationID)
    {
        $station = FillingStation::where('id', $stationID)->first();
        if (!$station) {
            throw new Client\NotFoundException('加油站信息不存在');
        }

        $areaInfo                 = app(Area::class)->getInfo($stationData['area_code']);
        $stationData['area_name'] = $areaInfo['full_name'];

        try {
            if ($station->fill($stationData)->save()) {
                return $station;
            }
        } catch (\Throwable $e) {
        }

        throw new Server\InternalServerException('车辆信息保存失败');
    }
}