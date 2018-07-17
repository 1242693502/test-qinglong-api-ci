<?php

namespace App\Http\Controllers\InternalApi\Initial\Truck;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Initial\Truck\TypeResource;

/**
 * Class TypeController
 *
 * @package App\Http\Controllers\InternalApi\Initial\Truck
 */
class TypeController extends BaseController
{
    /**
     * 获取车型列表
     *
     * @param int $type
     *
     * @return \App\Http\Resources\InternalApi\Initial\Truck\TypeResource[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function index($type = 0)
    {
        $truckTypes = app('file-db')->load('truck.types');
        if ($type) {
            $truckTypes = $truckTypes->where('type', $type);
        }

        return TypeResource::collection($truckTypes);
    }

}