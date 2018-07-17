<?php

namespace App\Services\Trailer;

use App\Models\Trailer\Trailer;
use App\Services\BaseService;
use Urland\Exceptions\Client;
use QingLong\Validate\License;
use Urland\Exceptions\Server\InternalServerException;

/**
 * Class TrailerService
 *
 * @package App\Services\Trailer
 */
class TrailerService extends BaseService
{

    /**
     * 创建挂车
     *
     * @param $trailerData
     *
     * @return \App\Models\Trailer\Trailer
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Urland\Exceptions\Client\ValidationException
     */
    public function create($trailerData)
    {
        $certificates = $trailerData['certificates'];
        unset($trailerData['certificates']);

        //1. 检查车牌是否符合规则
        //2. 车长 车型 识别和补充
        $trailerData = $this->fillTrailerData($trailerData);

        $trailer = Trailer::where('license_plate_number', $trailerData['license_plate_number'])->first(['id']);
        if ($trailer) {
            throw  Client\ValidationException::withMessages(['license_plate_number' => '车辆已存在']);
        }

        $trailer = Trailer::create($trailerData);
        // 保存证件照
        (new TrailerCertificateService())->updateOrCreate($trailer['trailer_uuid'], $certificates);
        return $trailer;

    }

    /**
     * 更新车辆信息
     *
     * @param string $trailerUUID
     * @param array  $trailerData
     *
     * @return \App\Models\Trailer\Trailer
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Urland\Exceptions\Client\ValidationException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function update($trailerUUID, array $trailerData)
    {
        $trailer = Trailer::where('trailer_uuid', $trailerUUID)->first();
        if (!$trailer) {
            throw Client\ValidationException::withMessages(['trailer_uuid' => '车辆不存在']);
        }

        $certificates = $trailerData['certificates'];
        unset($trailerData['certificates']);
        $trailerData = $this->fillTrailerData($trailerData);

        try {
            if ($trailer->fill($trailerData)->save()) {
                // 保存证件照
                (new TrailerCertificateService())->updateOrCreate($trailerUUID, $certificates);
                return $trailer;
            }
        } catch (\Throwable $e) {
        }

        throw new InternalServerException('车辆信息保存失败');

    }

    /**
     * 填充车辆信息
     *
     * @param $trailerData
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function fillTrailerData($trailerData)
    {
        // 检查车牌是否符合规则
        if (isset($trailerData['license_plate_number'])) {
            $this->checkLicense($trailerData['license_plate_number']);
        }

        // 车长 车型 识别和补充
        $typeName   = $this->getTruckTypeName($trailerData['type_code']);
        $lengthName = $this->getTruckLengthName($trailerData['length_code']);

        // 补充车型名称、车长名称
        $trailerData['type_name']   = $typeName;
        $trailerData['length_name'] = $lengthName;

        return $trailerData;
    }

    /**
     * 验证车牌
     *
     * @param $license string 车牌号
     */
    private function checkLicense($license)
    {
        if (!License::checkTrailer($license)) {
            throw Client\ValidationException::withMessages(['license_plate_number' => '车牌错误']);
        }
    }

    /**
     * 根据车辆编号获取车辆编码名称
     *
     * @param $typeCode string 车辆编号
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function getTruckTypeName($typeCode)
    {
        $type = app('file-db')->load('truck.types')->where('type', cons('uuid.trailer'))->firstWhere('code', $typeCode);
        if (!$type) {
            throw Client\ValidationException::withMessages(['type_code' => '车辆编码不存在']);
        }
        return $type['name'];
    }

    /**
     * 根据车辆长度编码获取车辆车长编码名称
     *
     * @param $lengthCode string 车辆长度编码
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function getTruckLengthName($lengthCode)
    {
        $length = app('file-db')->load('truck.lengths')->where('type', cons('uuid.trailer'))->firstWhere('code',
            $lengthCode);
        if (!$length) {
            throw Client\ValidationException::withMessages(['length_code' => '车辆长度编码不存在']);
        }
        return $length['name'];
    }

}