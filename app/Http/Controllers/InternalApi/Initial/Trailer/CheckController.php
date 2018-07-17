<?php

namespace App\Http\Controllers\InternalApi\Initial\Trailer;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Initial\Trailer\CheckResource;

class CheckController extends BaseController
{
    /**
     * 检查挂车
     *
     * @return \App\Http\Resources\InternalApi\Initial\Trailer\CheckResource[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function index()
    {
        $checkCertificates = app('file-db')->load('trailer.checks');

        return CheckResource::collection($checkCertificates);
    }

}