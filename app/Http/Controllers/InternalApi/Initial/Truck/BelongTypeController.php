<?php

namespace App\Http\Controllers\InternalApi\Initial\Truck;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Initial\Truck\BelongTypeResource;

/**
 * Class BelongTypeController
 *
 * @package App\Http\Controllers\InternalApi\Initial\Truck
 */
class BelongTypeController extends BaseController
{
    /**
     * 车辆归属类型列表
     *
     * @return \App\Http\Resources\InternalApi\Initial\Truck\BelongTypeResource[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function index()
    {
        $truckBelongTypes = app('file-db')->load('truck.belong_types');
        return BelongTypeResource::collection($truckBelongTypes);
    }

}