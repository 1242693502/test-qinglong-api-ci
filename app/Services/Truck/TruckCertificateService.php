<?php

namespace App\Services\Truck;

use App\Models\Truck\TruckCertificate;
use App\Services\BaseService;

class TruckCertificateService extends BaseService
{
    /**
     * 更新或创建车辆证件照
     *
     * @param string $truckUUID
     * @param array  $certificates
     *
     * @return bool
     */
    public function updateOrCreate($truckUUID, $certificates = [])
    {
        $certificateType = app('file-db')->load('truck.certificate_types')->where('type', cons('uuid.truck'));

        foreach ($certificates as $item) {
            $certificateInfo = $certificateType->firstWhere('code', $item['code']);
            if (!$certificateInfo) {
                continue;
            }
            TruckCertificate::updateOrCreate([
                'truck_uuid' => $truckUUID,
                'code'       => $item['code'],
            ], [
                'name'  => $certificateInfo['name'],
                'image' => $item['image'],
            ]);
        }

        return true;
    }
}