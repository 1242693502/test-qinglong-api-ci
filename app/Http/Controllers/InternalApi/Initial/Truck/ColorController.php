<?php

namespace App\Http\Controllers\InternalApi\Initial\Truck;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Initial\Truck\ColorResource;

/**
 * Class ColorController
 *
 * @package App\Http\Controllers\InternalApi\Initial\Truck
 */
class ColorController extends BaseController
{
    /**
     * 车辆颜色列表
     *
     * @return \App\Http\Resources\InternalApi\Initial\Truck\ColorResource[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function index()
    {
        $truckColors = app('file-db')->load('truck.colors');
        return ColorResource::collection($truckColors);
    }

}