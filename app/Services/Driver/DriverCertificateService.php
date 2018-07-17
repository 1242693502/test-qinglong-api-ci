<?php

namespace App\Services\Driver;

use App\Models\Driver\DriverCertificate;
use App\Services\BaseService;

class DriverCertificateService extends BaseService
{
    /**
     * 更新或创建司机证件照
     *
     * @param string $driverUUID
     * @param array  $certificates
     *
     * @return bool
     */
    public function updateOrCreate($driverUUID, $certificates = [])
    {
        $certificateType = app('file-db')->load('driver.certificate_types');

        foreach ($certificates as $item) {
            $certificateInfo = $certificateType->firstWhere('code', $item['code']);
            if (!$certificateInfo) {
                continue;
            }
            DriverCertificate::updateOrCreate([
                'driver_uuid' => $driverUUID,
                'code'        => $item['code'],
            ], [
                'name'  => $certificateInfo['name'],
                'image' => $item['image'],
            ]);
        }

        return true;
    }
}