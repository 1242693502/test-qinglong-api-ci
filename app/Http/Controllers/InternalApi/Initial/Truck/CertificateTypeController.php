<?php

namespace App\Http\Controllers\InternalApi\Initial\Truck;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Initial\Truck\CertificateTypeResource;

class CertificateTypeController extends BaseController
{
    /**
     * 根据车辆类型获取证件照类型列表
     *
     * @param int $type
     *
     * @return \App\Http\Resources\InternalApi\Initial\Truck\CertificateTypeResource[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function index($type = 0)
    {
        $truckCertificateTypes = app('file-db')->load('truck.certificate_types');
        if ($type) {
            $truckCertificateTypes = $truckCertificateTypes->where('type', $type);
        }
        return CertificateTypeResource::collection($truckCertificateTypes);
    }

}