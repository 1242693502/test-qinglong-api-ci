<?php

namespace Tests\Unit\Services\GasCart;

use App\Models\GasCard\FillingStation;
use App\Services\GasCard\FillingStationService;
use Tests\TestCase;

class FillingStationTest extends TestCase
{

    /**
     * 测试创建加油站
     *
     * @return mixed
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Client\ValidationException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testCreate()
    {
        $stationData = [
            'name'      => '王府井加油站',
            'area_code' => '110101001',
            'address'   => '讯美科技广场1号楼1201',
            'longitude' => '116.116',
            'latitude'  => '110.11',
        ];

        $fillingStation = (new FillingStationService())->create($stationData);

        //判断是否返回的类名是否正确
        $this->assertTrue($fillingStation instanceof FillingStation);

        $this->assertEquals($stationData['name'], $fillingStation->name);

        $this->assertEquals($stationData['area_code'], $fillingStation->area_code);

        $this->assertEquals($stationData['address'], $fillingStation->address);

        return $fillingStation;
    }

    /**
     * 测试更新加油站
     *
     * @return mixed
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Urland\Exceptions\Client\NotFoundException
     * @throws \Urland\Exceptions\Server\InternalServerException
     */
    public function testUpdate()
    {
        $fillingStation = FillingStation::where('id', '1')->first();
        $stationID      = $fillingStation->id;
        $stationData    = [
            'name'      => '天安门加油站',
            'area_code' => '110101001',
            'address'   => '讯美科技广场1号楼1202',
            'longitude' => '116.116',
            'latitude'  => '110.11',
        ];

        $fillingStation = (new FillingStationService())->update($stationData, $stationID);

        //判断是否返回的类名是否正确
        $this->assertTrue($fillingStation instanceof FillingStation);

        $this->assertEquals($stationData['name'], $fillingStation->name);

        $this->assertEquals($stationData['address'], $fillingStation->address);

        return $fillingStation;
    }

}
