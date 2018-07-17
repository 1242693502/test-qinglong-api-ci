<?php

namespace Tests\Unit\Services\Driver;


use App\Models\Driver\Driver;
use App\Services\Driver\DriverService;
use Tests\TestCase;
use Urland\Exceptions\Client\ValidationException;

class DriverTest extends TestCase
{

    /**
     * 测试创建司机方法
     *
     * @return \App\Services\Driver\DriverService|\Illuminate\Database\Eloquent\Model|null|object
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public function testCreate_1()
    {
        $driverName      = '张三';
        $driverJobNumber = '333222111';
        $driverPhone     = '18888888888';
        $driverIdNumber  = '326474975853671210';
        $driverData      = [
            'name'                 => $driverName,
            'job_number'           => $driverJobNumber,
            'phone'                => $driverPhone,
            'id_number'            => $driverIdNumber,
            'driver_license_type'  => 'A1',
            'contact_address_code' => '110101001',
            'contact_address_name' => '某镇',
            'certificates'         => [
                [
                    'code'  => '300',
                    'image' => 'md5'
                ]
            ]
        ];

        $driver = (new DriverService())->create($driverData);

        // 判断返回的类名是否正确
        $this->assertTrue($driver instanceof Driver);

        // 判断司机身份证号是否正确
        $this->assertEquals($driverIdNumber, $driver->id_number);

        // 判断司机手机号是否正确
        $this->assertEquals($driverPhone, $driver->phone);

        return $driver;

    }

    /**
     * 测试创建司机方法
     *
     * @return \App\Services\Driver\DriverService|\Illuminate\Database\Eloquent\Model|null|object
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public function testCreate_2()
    {
        $driverName      = '李四';
        $driverJobNumber = '333222122';
        $driverPhone     = '18888888899';
        $driverIdNumber  = '326474975853671211';
        $driverData      = [
            'name'                 => $driverName,
            'job_number'           => $driverJobNumber,
            'phone'                => $driverPhone,
            'id_number'            => $driverIdNumber,
            'driver_license_type'  => 'A1',
            'contact_address_code' => '110101001',
            'contact_address_name' => '某镇',
            'certificates'         => [
                [
                    'code'  => '300',
                    'image' => 'md5'
                ]
            ]
        ];

        $driver = (new DriverService())->create($driverData);

        // 判断返回的类名是否正确
        $this->assertTrue($driver instanceof Driver);

        // 判断司机身份证号是否正确
        $this->assertEquals($driverIdNumber, $driver->id_number);

        // 判断司机手机号是否正确
        $this->assertEquals($driverPhone, $driver->phone);

        return $driver;

    }

    /**
     * 测试更新司机方法
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testUpdate_1()
    {
        $driver = factory(Driver::class)->create();

        $driverUUID      = $driver->driver_uuid;
        $driverName      = '张三2';
        $driverJobNumber = '333222111';
        $driverIdNumber  = '326474975853671209';
        $driverData      = [
            'name'                 => $driverName,
            'job_number'           => $driverJobNumber,
            'id_number'            => $driverIdNumber,
            'driver_license_type'  => 'A2',
            'contact_address_code' => '110101001',
            'contact_address_name' => '某镇',
            'certificates'         => [
                [
                    'code'  => '300',
                    'image' => 'md5'
                ]
            ]
        ];

        $driver = (new DriverService())->update($driverUUID, $driverData);

        // 判断返回的类名是否正确
        $this->assertTrue($driver instanceof Driver);

        // 判断司机身份证号是否正确
        $this->assertEquals($driverIdNumber, $driver->id_number);

        return $driver;

    }

    /**
     * 测试创建司机方法失败
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \InvalidArgumentException
     * @throws \Urland\Exceptions\Client\ForbiddenException
     * @throws \Urland\Exceptions\Client\NotFoundException
     */
    public function testCreateFail()
    {
        $driverName      = '张三';
        $driverJobNumber = '333222111';
        $driverPhone     = '18888888888';
        $driverIdNumber  = '326474975853671210';
        $driverData      = [
            'name'                 => $driverName,
            'job_number'           => $driverJobNumber,
            'phone'                => $driverPhone,
            'id_number'            => $driverIdNumber,
            'driver_license_type'  => 'A1',
            'contact_address_code' => '110101001',
            'contact_address_name' => '某镇',
            'certificates'         => [
                [
                    'code'  => '300',
                    'image' => 'md5'
                ]
            ]
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('参数验证失败');
        $driver = (new DriverService())->create($driverData);

        return true;

    }


}