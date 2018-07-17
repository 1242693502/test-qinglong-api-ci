<?php

namespace App\Http\Controllers\InternalApi\Initial\Truck;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Initial\Truck\CheckCertificateResource;

class CheckCertificateController extends BaseController
{
    /**
     * 检查车辆证件
     *
     * @return \App\Http\Resources\InternalApi\Initial\Truck\CheckCertificateResource[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function index()
    {
        $checkCertificates = app('file-db')->load('truck.check_certificates');

        return CheckCertificateResource::collection($checkCertificates);
    }

}