<?php

namespace App\Http\Controllers\InternalApi\Initial\Driver;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Initial\Driver\CertificateTypeResource;

class CertificateTypeController extends BaseController
{
    /**
     * 证件类型列表
     *
     * @return \App\Http\Resources\InternalApi\Initial\Driver\CertificateTypeResource[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function index()
    {
        $driverCertificateTypes = app('file-db')->load('driver.certificate_types');
        return CertificateTypeResource::collection($driverCertificateTypes);
    }
}