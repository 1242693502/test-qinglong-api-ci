<?php

namespace Tests\Unit\Services\Truck;

use Tests\TestCase;
use App\Services\Truck\TruckService;
use App\Models\Truck\Truck;
use Urland\Exceptions\Client\ValidationException;

class TruckTest extends TestCase
{

    /**
     * 测试创建车辆方法
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\ValidationException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function testCreate_1()
    {
        $truckPlateNumber = '粤ABX001';
        $truckTypeCode    = 'H01';
        $truckLengthCode  = '38';
        $truckData        = [
            'license_plate_number' => $truckPlateNumber,
            'belong_type'          => 1,
            'brand'                => '欧马可',
            'engine_number'        => '245369K',
            'axle_number'          => 2,
            'type_code'            => $truckTypeCode,
            'length_code'          => $truckLengthCode,
            'vin'                  => 'K233',
            'owner_name'           => '张三',
            'body_color'           => ' 红色',
            'approved_tonnage'     => 1800,
            'actual_tonnage'       => 1500,
            'purchase_time'        => '2017-05-22 16:35:15',
            'purchase_price'       => 100000,
            'certificates'         => [
                [
                    'code'  => '300',
                    'image' => 'md5'
                ]
            ]
        ];

        $truck = (new TruckService())->create($truckData);

        //判断是否返回的类名是否正确
        $this->assertTrue($truck instanceof Truck);

        //判断车辆名称是否正确
        $this->assertEquals($truckPlateNumber, $truck->license_plate_number);

        // 判断车辆编号是否正确
        $this->assertEquals($truckTypeCode, $truck->type_code);

        // 判断车辆车长编号是否正确
        $this->assertEquals($truckLengthCode, $truck->length_code);

        return $truck;
    }

    /**
     * 测试创建车辆方法
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\ValidationException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function testCreate_2()
    {
        $truckPlateNumber = '粤ABX002';
        $truckTypeCode    = 'H01';
        $truckLengthCode  = '38';
        $truckData        = [
            'license_plate_number' => $truckPlateNumber,
            'belong_type'          => 1,
            'brand'                => '欧马可',
            'engine_number'        => '245369K',
            'axle_number'          => 2,
            'type_code'            => $truckTypeCode,
            'length_code'          => $truckLengthCode,
            'vin'                  => 'K233',
            'owner_name'           => '张三',
            'body_color'           => ' 红色',
            'approved_tonnage'     => 1800,
            'actual_tonnage'       => 1500,
            'purchase_time'        => '2017-05-22 16:35:15',
            'purchase_price'       => 100000,
            'truck_status'       => 10,
            'certificates'         => [
                [
                    'code'  => '300',
                    'image' => 'md5'
                ]
            ]
        ];

        $truck = (new TruckService())->create($truckData);

        //判断是否返回的类名是否正确
        $this->assertTrue($truck instanceof Truck);

        //判断车辆名称是否正确
        $this->assertEquals($truckPlateNumber, $truck->license_plate_number);

        // 判断车辆编号是否正确
        $this->assertEquals($truckTypeCode, $truck->type_code);

        // 判断车辆车长编号是否正确
        $this->assertEquals($truckLengthCode, $truck->length_code);

        $truck->setAttribute('truck_status',$truckData['truck_status'])->save();

        return $truck;
    }

    /**
     * 测试车辆更新方法
     *
     * @param $truckUUID
     *
     * @return \App\Models\Truck\Truck|mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\ValidationException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testUpdate_1()
    {
        $truck = factory(Truck::class)->create();

        $truckUUID        = $truck->truck_uuid;
        $truckPlateNumber = '粤AB12345';
        $truckTypeCode    = 'H01';
        $truckLengthCode  = '38';
        $truckData        = [
            'license_plate_number' => $truckPlateNumber,
            'belong_type'          => 1,
            'brand'                => '欧马可',
            'engine_number'        => '245369K',
            'axle_number'          => 2,
            'type_code'            => $truckTypeCode,
            'length_code'          => $truckLengthCode,
            'vin'                  => 'K233',
            'body_color'           => ' 红色',
            'approved_tonnage'     => 1800,
            'actual_tonnage'       => 1500,
        ];

        $truck = (new TruckService())->update($truckUUID, $truckData);

        //判断是否返回的类名是否正确
        $this->assertTrue($truck instanceof Truck);

        //判断车辆名称是否正确
        $this->assertEquals($truckPlateNumber, $truck->license_plate_number);

        // 判断车辆编号是否正确
        $this->assertEquals($truckTypeCode, $truck->type_code);

        // 判断车辆车长编号是否正确
        $this->assertEquals($truckLengthCode, $truck->length_code);

        return $truck;
    }

    /**
     * 测试创建车辆方法失败
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Urland\Exceptions\Client\ValidationException
     */
    public function testCreateFail()
    {
        $truckPlateNumber = '粤ABX001';
        $truckTypeCode    = 'H01';
        $truckLengthCode  = '38';
        $truckData        = [
            'license_plate_number' => $truckPlateNumber,
            'belong_type'          => 1,
            'brand'                => '欧马可',
            'engine_number'        => '245369K',
            'axle_number'          => 2,
            'type_code'            => $truckTypeCode,
            'length_code'          => $truckLengthCode,
            'vin'                  => 'K233',
            'owner_name'           => '张三',
            'body_color'           => ' 红色',
            'approved_tonnage'     => 1800,
            'actual_tonnage'       => 1500,
            'purchase_time'        => '2017-05-22 16:35:15',
            'purchase_price'       => 100000,
            'certificates'         => [
                [
                    'code'  => '300',
                    'image' => 'md5'
                ]
            ]
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('参数验证失败');
        $truck = (new TruckService())->create($truckData);

        return true;
    }

}
