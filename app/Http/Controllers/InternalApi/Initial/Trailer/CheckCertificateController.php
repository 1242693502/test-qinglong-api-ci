<?php

namespace App\Http\Controllers\InternalApi\Initial\Trailer;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Initial\Trailer\CheckCertificateResource;

class CheckCertificateController extends BaseController
{
    /**
     * 检查挂车证件
     *
     * @return \App\Http\Resources\InternalApi\Initial\Trailer\CheckCertificateResource[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function index()
    {
        $checkCertificates = app('file-db')->load('trailer.check_certificates');

        return CheckCertificateResource::collection($checkCertificates);
    }

}