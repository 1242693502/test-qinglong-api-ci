<?php

namespace App\Http\Controllers\InternalApi\Initial\Driver;

use App\Http\Controllers\InternalApi\BaseController;
use App\Http\Resources\InternalApi\Initial\Driver\LicenseTypeResource;

/**
 * Class LicenseTypeController
 *
 * @package App\Http\Controllers\InternalApi\Initial\Driver
 */
class LicenseTypeController extends BaseController
{
    /**
     * 证件类型列表
     *
     * @return \App\Http\Resources\InternalApi\Initial\Driver\LicenseTypeResource[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function index()
    {
        $driverLicenseTypes = app('file-db')->load('driver.license_types');
        return LicenseTypeResource::collection($driverLicenseTypes);
    }
}