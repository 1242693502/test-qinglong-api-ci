<?php

namespace App\Http\Controllers\InternalApi\Initial\Truck;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Initial\Truck\CheckResource;

class CheckController extends BaseController
{
    /**
     * 检查车辆
     *
     * @return \App\Http\Resources\InternalApi\Initial\Truck\CheckResource[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function index()
    {
        $checkCertificates = app('file-db')->load('truck.checks');

        return CheckResource::collection($checkCertificates);
    }

}