<?php

namespace App\Http\Controllers\InternalApi\Initial\Truck;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Initial\Truck\LengthResource;

/**
 * Class LengthController
 *
 * @package App\Http\Controllers\InternalApi\Initial\Truck
 */
class LengthController extends BaseController
{
    /**
     * 获取车长列表
     *
     * @param int $type
     *
     * @return \App\Http\Resources\InternalApi\Initial\Truck\LengthResource[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function index($type = 0)
    {
        $truckLengths = app('file-db')->load('truck.lengths');
        if ($type) {
            $truckLengths = $truckLengths->where('type', $type);
        }
        return LengthResource::collection($truckLengths);
    }

}