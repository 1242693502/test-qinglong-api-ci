<?php

namespace App\Services\Trailer;

use App\Models\Trailer\TrailerCertificate;
use App\Services\BaseService;

class TrailerCertificateService extends BaseService
{
    /**
     * 更新或创建挂车证件照
     *
     * @param string $trailerUUID
     * @param array  $certificates
     *
     * @return bool
     */
    public function updateOrCreate($trailerUUID, $certificates = [])
    {
        $certificateType = app('file-db')->load('truck.certificate_types')->where('type', cons('uuid.trailer'));

        foreach ($certificates as $item) {
            $certificateInfo = $certificateType->firstWhere('code', $item['code']);
            if (!$certificateInfo) {
                continue;
            }
            TrailerCertificate::updateOrCreate([
                'trailer_uuid' => $trailerUUID,
                'code'         => $item['code'],
            ], [
                'name'  => $certificateInfo['name'],
                'image' => $item['image'],
            ]);
        }

        return true;
    }
}