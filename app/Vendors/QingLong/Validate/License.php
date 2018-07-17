<?php

namespace QingLong\Validate;

/**
 * 验证车辆车牌
 *
 * Class License
 * @package Come56\Validate
 */
class License
{

    /**
     * 验证普通车牌
     *
     * @param $license string 车牌号
     *
     * @return bool
     */
    public static function checkTruck($license)
    {
        $regular = '/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]{1}[A-Z]{1}[0-9a-zA-Z]{5,6}$/u';
        return self::check($regular, $license);
    }

    /**
     * 验证挂车车牌
     *
     * @param $license string 车牌号
     *
     * @return bool
     */
    public static function checkTrailer($license)
    {
        $regular = '/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]{1}[A-Z]{1}[0-9a-zA-Z]{4}[挂]{1}$/u';
        return self::check($regular, $license);
    }

    /**
     * 匹配车牌
     *
     * @param $regular string 匹配规则
     * @param $license string 车牌号
     *
     * @return bool
     */
    private static function check($regular, $license)
    {
        preg_match($regular, $license, $match);
        return isset($match[0]);
    }
}