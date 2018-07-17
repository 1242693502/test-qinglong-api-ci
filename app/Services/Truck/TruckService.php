<?php

namespace App\Services\Truck;

use App\Models\Truck\Truck;
use App\Services\BaseService;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;
use QingLong\Validate\License;

/**
 * Class TruckService
 *
 * @package App\Services\Truck
 */
class TruckService extends BaseService
{
    /**
     * 通过UUID获取车辆信息
     *
     * @param       $truckUUID
     * @param array $fields
     *
     * @return \App\Models\Truck\Truck|null
     */
    public function getInfoByUUID($truckUUID, $fields = [])
    {
        return Truck::where('truck_uuid', $truckUUID)->first($fields);
    }

    /**
     * 创建车辆
     *
     * @param array $truckData
     *
     * @return \App\Models\Truck\Truck
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Urland\Exceptions\Client\ValidationException
     */
    public function create($truckData)
    {
        //TODO
        //1. 检查车牌是否符合规则
        //2. 车长 车型 识别和补充
        $truckData = $this->fillTruckData($truckData);

        // TODO: 是否应该将所有与表单校验相关的逻辑都放Request
        if (Truck::where('license_plate_number', $truckData['license_plate_number'])->first(['id'])) {
            throw Client\ValidationException::withMessages(['license_plate_number' => '车辆已存在']);
        }

        $certificates = $truckData['certificates'];
        unset($truckData['certificates']);

        $truck = Truck::create($truckData);
        // 保存证件照
        (new TruckCertificateService())->updateOrCreate($truck['truck_uuid'], $certificates);

        return $truck;
    }

    /**
     * 更新车辆信息
     *
     * @param string $truckUUID
     * @param array  $truckData
     *
     * @return \App\Models\Truck\Truck
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Urland\Exceptions\Client\ValidationException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function update($truckUUID, array $truckData)
    {
        $truck = Truck::where('truck_uuid', $truckUUID)->first();
        if (!$truck) {
            throw Client\ValidationException::withMessages(['truck_uuid' => '车辆不存在']);
        }

        $certificates = [];
        if (isset($truckData['certificates'])) {
            $certificates = $truckData['certificates'];
            unset($truckData['certificates']);
        }

        //1. 检查车牌是否符合规则
        //2. 车长 车型 识别和补充
        $truckData = $this->fillTruckData($truckData);

        try {
            if ($truck->fill($truckData)->save()) {
                // 保存证件照
                (new TruckCertificateService())->updateOrCreate($truckUUID, $certificates);
                return $truck;
            }
        } catch (\Throwable $e) {
        }

        throw new Server\InternalServerException('车辆信息保存失败');
    }

    /**
     * 检查车辆是否有效
     *
     * @param $truckUUID
     *
     * @return mixed
     */
    public function checkTruckUUID($truckUUID)
    {
        $truck = Truck::where('truck_uuid', $truckUUID)->first(['truck_uuid']);
        if (!$truck) {
            return false;
        }
        return true;
    }

    /**
     * 填充车辆信息
     *
     * @param $truckData
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function fillTruckData($truckData)
    {
        // 检查车牌是否符合规则
        if (isset($truckData['license_plate_number'])) {
            $this->checkLicense($truckData['license_plate_number']);
        }

        // 车长 车型 识别和补充
        $typeName   = $this->getTruckTypeName($truckData['type_code']);
        $lengthName = $this->getTruckLengthName($truckData['length_code']);

        // 补充车型名称、车长名称
        $truckData['type_name']   = $typeName;
        $truckData['length_name'] = $lengthName;

        return $truckData;
    }

    /**
     * 验证车牌
     *
     * @param $license string 车牌号
     */
    private function checkLicense($license)
    {
        if (!License::checkTruck($license)) {
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
        $type = app('file-db')->load('truck.types')->where('type', cons('uuid.truck'))->firstWhere('code', $typeCode);
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
        $length = app('file-db')->load('truck.lengths')->where('type', cons('uuid.truck'))->firstWhere('code',
            $lengthCode);
        if (!$length) {
            throw Client\ValidationException::withMessages(['length_code' => '车辆长度编码不存在']);
        }
        return $length['name'];
    }
}