<?php

namespace App\Http\Controllers\InternalApi\GasCard;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Requests\InternalApi\GasCard\FillingStation\CreateRequest;
use App\Http\Requests\InternalApi\GasCard\FillingStation\UpdateRequest;
use App\Http\Resources\InternalApi\GasCard\FillingStationResource;
use App\Services\GasCard\FillingStationService;

/**
 * 加油站接口类
 * Class FillingStationController
 *
 * @package App\Http\Controllers\InternalApi\GasCard
 */
class FillingStationController extends BaseController
{
    /**
     * 创建加油站
     *
     * @param \App\Http\Requests\InternalApi\GasCard\FillingStation\CreateRequest $request
     *
     * @return \App\Http\Resources\InternalApi\GasCard\FillingStationResource
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Client\ValidationException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function store(CreateRequest $request)
    {
        $station = (new FillingStationService())->create($request->validated());

        return new FillingStationResource($station);
    }

    /**
     * 更新加油站
     *
     * @param \App\Http\Requests\InternalApi\GasCard\FillingStation\UpdateRequest $request
     * @param                                                                     $stationID
     *
     * @return \App\Http\Resources\InternalApi\GasCard\FillingStationResource
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function update(UpdateRequest $request, $stationID)
    {
        $station = (new FillingStationService())->update($request->validated(), $stationID);

        return new FillingStationResource($station);
    }
}