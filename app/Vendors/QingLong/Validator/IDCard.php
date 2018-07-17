<?php

namespace QingLong\Validator;

/**
 * 身份证校验类
 * Class IDCard
 *
 * @package QingLong\Validator
 */
class IDCard
{
    /**
     * 验证身份证是否有效
     *
     * @param $IDCard
     *
     * @return bool
     */
    public static function validateIDCard($IDCard)
    {
        $length = strlen($IDCard);
        if ($length == 15) {
            $IDCard = self::_convertIDCard15to18($IDCard);
        }
        return self::_check18IDCard($IDCard);
    }

    /**
     * 将15位身份证升级到18位
     *
     * @param $IDCard
     *
     * @return bool|string
     */
    private static function _convertIDCard15to18($IDCard)
    {
        if (strlen($IDCard) != 15) {
            return false;
        } else {
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search(substr($IDCard, 12, 3), ['996', '997', '998', '999']) !== false) {
                $IDCard = substr($IDCard, 0, 6) . '18' . substr($IDCard, 6, 9);
            } else {
                $IDCard = substr($IDCard, 0, 6) . '19' . substr($IDCard, 6, 9);
            }
        }
        $IDCard = $IDCard . self::_calcIDCardCode($IDCard);
        return $IDCard;
    }

    /**
     * 18位身份证校验码有效性检查
     *
     * @param $IDCard
     *
     * @return bool
     */
    private static function _check18IDCard($IDCard)
    {
        if (strlen($IDCard) != 18) {
            return false;
        }

        $IDCardBody = substr($IDCard, 0, 17); //身份证主体
        $IDCardCode = strtoupper(substr($IDCard, 17, 1)); //身份证最后一位的验证码

        if (self::_calcIDCardCode($IDCardBody) != $IDCardCode) {
            return false;
        }
        return true;
    }

    /**
     * 计算身份证的最后一位验证码,根据国家标准GB 11643-1999
     *
     * @param $IDCardBody
     *
     * @return bool
     */
    private static function _calcIDCardCode($IDCardBody)
    {
        if (strlen($IDCardBody) != 17) {
            return false;
        }

        //加权因子
        $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        //校验码对应值
        $code     = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
        $checksum = 0;

        for ($i = 0; $i < strlen($IDCardBody); $i++) {
            $checksum += substr($IDCardBody, $i, 1) * $factor[$i];
        }

        return $code[$checksum % 11];
    }
}