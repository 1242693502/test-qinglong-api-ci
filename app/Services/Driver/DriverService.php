<?php

namespace App\Services\Driver;

use App\Models\Driver\Driver;
use App\Services\BaseService;
use QingLong\Platform\Area\Area;
use Urland\Exceptions\Client;
use Urland\Exceptions\Server;

/**
 * Class DriverService
 *
 * @package App\Services\Driver
 */
class DriverService extends BaseService
{
    /**
     * 创建司机
     *
     * @param array $driverData
     *
     * @return $this|\Illuminate\Database\Eloquent\Model|null|object|static
     * @throws Client\ForbiddenException
     * @throws Client\NotFoundException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \InvalidArgumentException
     */
    public function create(array $driverData)
    {
        // TODO
        // 1. 补充手机号认证状态、身份证认证状态
        // 2. 补充开户日期

        $driver = Driver::where('phone', $driverData['phone'])->first(['id']);
        if ($driver) {
            throw Client\ValidationException::withMessages(['phone' => '该手机号已注册过']);
        }

        $driver = Driver::where('id_number', $driverData['id_number'])->first(['id']);
        if ($driver) {
            throw Client\ValidationException::withMessages(['id_number' => '该身份证已被添加']);
        }

        // 检测驾照类型
        $this->checkLicenseType($driverData['driver_license_type']);

        // 补充联系地址镇/街道级编码
        $areaInfo                           = app(Area::class)->getFinalInfo($driverData['contact_address_code']);
        $driverData['contact_address_name'] = $areaInfo['full_name'];

        $certificates = $driverData['certificates'];
        unset($driverData['certificates']);

        $driver = Driver::create($driverData);

        // 保存证件照
        (new DriverCertificateService())->updateOrCreate($driver['driver_uuid'], $certificates);

        return $driver;
    }

    /**
     * 更新司机信息
     *
     * @param string $driverUUID
     * @param array  $driverData
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     * @throws Client\ForbiddenException
     * @throws Client\NotFoundException
     * @throws Server\InternalServerException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \InvalidArgumentException
     */
    public function update($driverUUID, array $driverData)
    {
        $driver = Driver::where('driver_uuid', $driverUUID)->first();
        if (!$driver) {
            throw Client\ValidationException::withMessages(['driver_uuid' => '司机不存在']);
        }

        // TODO： 身份证不能随便修改

        // 检测驾照类型
        $this->checkLicenseType($driverData['driver_license_type']);

        // 补充联系地址镇/街道级编码
        if ($driver['contact_address_code'] != $driverData['contact_address_code']) {
            $areaInfo                           = app(Area::class)->getFinalInfo($driverData['contact_address_code']);
            $driverData['contact_address_name'] = $areaInfo['full_name'];
        }

        // 司机身份认证
        $certificates = $driverData['certificates'];
        unset($driverData['certificates']);

        try {
            if ($driver->fill($driverData)->save()) {
                // 保存证件照
                (new DriverCertificateService())->updateOrCreate($driver['driver_uuid'], $certificates);
                return $driver;
            }
        } catch (\Throwable $e) {
        }

        throw new Server\InternalServerException('司机信息保存失败');

    }

    /**
     * 检测驾照类型
     *
     * @param $licenseType
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function checkLicenseType($licenseType)
    {
        if (app('file-db')->load('driver.license_types')->where('name', $licenseType)->isEmpty()) {
            throw Client\ValidationException::withMessages(['driver_license_type' => '驾照类型不存在']);
        }
        return $licenseType;
    }

}