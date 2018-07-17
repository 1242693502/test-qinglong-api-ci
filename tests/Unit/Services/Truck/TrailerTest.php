<?php

namespace Tests\Unit\Services\Trailer;


use App\Models\Trailer\Trailer;
use App\Services\Trailer\TrailerService;
use Tests\TestCase;
use Urland\Exceptions\Client\ValidationException;

class TrailerTest extends TestCase
{

    /**
     * 测试创建挂车方法
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\ValidationException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function testCreate_1()
    {
        $trailerPlateNumber = '粤A2555挂';
        $trailerTypeCode    = 'G01';
        $trailerLengthCode  = '20';
        $trailerData        = [
            'license_plate_number' => $trailerPlateNumber,
            'belong_type'          => 1,
            'brand'                => '欧马可',
            'engine_number'        => '555555',
            'axle_number'          => 2,
            'type_code'            => $trailerTypeCode,
            'length_code'          => $trailerLengthCode,
            'vin'                  => 'K233',
            'owner_name'           => '张三',
            'body_color'           => ' 红色',
            'approved_tonnage'     => 1800,
            'actual_tonnage'       => 1500,
            'purchase_time'        => '2017-05-22 16:35:15',
            'purchase_price'       => 100000,
            'certificates'         => [
                ['code' => '400', 'image' => 'md5'],
                ['code' => '410', 'image' => '1111'],
                ['code' => '420', 'image' => '2222'],
            ]
        ];

        $trailer = (new TrailerService())->create($trailerData);

        //判断是否返回的类名是否正确
        $this->assertTrue($trailer instanceof Trailer);

        //判断车辆名称是否正确
        $this->assertEquals($trailerPlateNumber, $trailer->license_plate_number);

        // 判断车辆编号是否正确
        $this->assertEquals($trailerTypeCode, $trailer->type_code);

        // 判断车辆车长编号是否正确
        $this->assertEquals($trailerLengthCode, $trailer->length_code);

        Trailer::destroy($trailer->id);
    }

    /**
     * 测试挂车更新方法
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Urland\Exceptions\Client\ValidationException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testUpdate_1()
    {
        $trailer            = factory(Trailer::class)->create();
        $trailerUUID        = $trailer->trailer_uuid;
        $trailerPlateNumber = '粤A2555挂';
        $trailerTypeCode    = 'G01';
        $trailerLengthCode  = '20';
        $trailerData        = [
            'license_plate_number' => $trailerPlateNumber,
            'belong_type'          => 1,
            'brand'                => '欧马可',
            'engine_number'        => '555555',
            'axle_number'          => 2,
            'type_code'            => $trailerTypeCode,
            'length_code'          => $trailerLengthCode,
            'vin'                  => 'K233',
            'owner_name'           => '张三',
            'body_color'           => ' 红色',
            'approved_tonnage'     => 1800,
            'actual_tonnage'       => 1500,
            'purchase_time'        => '2017-05-22 16:35:15',
            'purchase_price'       => 100000,
            'certificates'         => [
                ['code' => '400', 'image' => 'md5'],
                ['code' => '410', 'image' => '1111'],
                ['code' => '420', 'image' => '2222'],
            ]
        ];

        $trailer = (new TrailerService())->update($trailerUUID, $trailerData);

        //判断是否返回的类名是否正确
        $this->assertTrue($trailer instanceof Trailer);

        //判断车辆名称是否正确
        $this->assertEquals($trailerPlateNumber, $trailer->license_plate_number);

        // 判断车辆编号是否正确
        $this->assertEquals($trailerTypeCode, $trailer->type_code);

        // 判断车辆车长编号是否正确
        $this->assertEquals($trailerLengthCode, $trailer->length_code);
    }

    /**
     * 测试创建挂车方法失败
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Urland\Exceptions\Client\ValidationException
     */
    public function testCreateFail()
    {
        $trailerPlateNumber = '粤A2555挂';
        $trailerTypeCode    = 'G01';
        $trailerLengthCode  = '20';
        $trailerData        = [
            'license_plate_number' => $trailerPlateNumber,
            'belong_type'          => 1,
            'brand'                => '欧马可',
            'engine_number'        => '555555',
            'axle_number'          => 2,
            'type_code'            => $trailerTypeCode,
            'length_code'          => $trailerLengthCode,
            'vin'                  => 'K233',
            'owner_name'           => '张三',
            'body_color'           => ' 红色',
            'approved_tonnage'     => 1800,
            'actual_tonnage'       => 1500,
            'purchase_time'        => '2017-05-22 16:35:15',
            'purchase_price'       => 100000,
            'certificates'         => [
                ['code' => '400', 'image' => 'md5'],
                ['code' => '410', 'image' => '1111'],
                ['code' => '420', 'image' => '2222'],
            ]
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('参数验证失败');
        $trailer = (new TrailerService())->create($trailerData);

        return true;
    }

}