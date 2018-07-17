<?php

namespace QingLong\Platform\Area;

use Urland\Exceptions\Client\BadRequestException;
use Urland\Exceptions\Client\NotFoundException;
use Urland\Exceptions\Server\InternalServerException;

/**
 * Class Area
 * 获取列表：https://area.muniu.urland.cn/api/v1/areas
 * 列表压缩：https://area.muniu.urland.cn/api/v1/areas?compress=1
 * 获取所有省份：https://area.muniu.urland.cn/api/v1/areas?parent_code=1
 * 获取北京所有市：https://area.muniu.urland.cn/api/v1/areas?parent_code=110000
 * 获取单个记录：https://area.muniu.urland.cn/api/v1/areas/{code}
 *
 * @package QingLong\Platform\Area
 */
class Area
{
    /**
     * area实例
     *
     * @var null
     */
    protected $instance = null;

    protected $max_level = 5;

    protected $prefix = '/api/v1/';

    /**
     * Area constructor.
     *
     * @throws InternalServerException
     */
    public function __construct()
    {
        try {
            $this->instance = app('api-client')->service('area-api');
        } catch (\Throwable $e) {
            throw new InternalServerException('连接地址库服务失败');
        }
    }

    /**
     * 根据地址编码获取地址信息
     *
     * @param string $areaCode
     *
     * @return array
     * @throws NotFoundException
     */
    public function getInfo($areaCode)
    {
        try {
            $uri      = $this->prefix . 'areas/' . $areaCode;
            $areaInfo = $this->instance->get($uri)->getJson();
        } catch (\Exception $e) {
            throw new NotFoundException('获取地址信息失败', $e);
        }
        return $areaInfo;
    }

    /**
     * 获取当前选择的地址是不是最终可选择的
     *
     * @param string $areaCode
     *
     * @return array
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getFinalInfo($areaCode)
    {
        $areaInfo = $this->getInfo($areaCode);
        // 最大层级数，直接返回
        if ($areaInfo['level'] === $this->max_level) {
            return $areaInfo;
        }

        try {
            $uri = $this->prefix . 'areas';

            $childrenList = $this->instance->get($uri, [
                'parent_code' => $areaCode,
                'compress'    => 1,
                'level'       => 5,
            ])->getJson();
        } catch (\Exception $e) {
            throw new NotFoundException('获取选择的地址信息失败', $e);
        }

        if (!empty($childrenList)) {
            throw new BadRequestException('请再选择下一级地址');
        }

        return $areaInfo;
    }
}