<?php

namespace App\Http\Controllers\InternalApi\Initial\Truck;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Initial\Truck\PlateResource;

class PlateController extends BaseController
{
    /**
     * 车辆颜色列表
     *
     * @return \App\Http\Resources\InternalApi\Initial\Truck\PlateResource[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function index()
    {
        $truckPlates = app('file-db')->load('truck.plates');
        return PlateResource::collection($truckPlates);
    }

}