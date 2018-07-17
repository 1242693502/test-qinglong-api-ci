<?php

namespace QingLong\UUID;

/**
 * 一个短UUID的生成库
 *
 * Class UUID
 */
class UUID
{
    const START_SEC = 1420041600; // 2015-1-1 0:0:0 的时间戳

    /**
     * 通过一个ID生成一个短UUID
     *
     * @param int $typeNo 类型编号，由调用者决定
     * @param int $id     调用者提供一个该类型下的自增长序列值，以保证 UUID 的唯一性
     *
     * @return string
     */
    public static function buildById($typeNo, $id)
    {
        //在2045年前，$baseTime 将保持 10位数字
        $baseTime = ceil((microtime(true) - self::START_SEC) * 10);
        //引入 自增索引ID 来保证 $baseUUID 唯一性
        //当ID小于 5000 万时, $baseUUID 保持 9 位数字:
        $baseUUID = (int)$baseTime + $id;
        //服务器标识，用于避免多服务器时间戳不同步导致的UUID重复问题 使用 1 位数字表示
        $serverNo = config('app.serverNo');

        //生成UUID，最后一位随机
        $UUID = $typeNo . $serverNo . $baseUUID . rand(0, 9);
        return $UUID;
    }

    /**
     * 生成luhn校验码
     * wiki: https://zh.wikipedia.org/wiki/Luhn%E7%AE%97%E6%B3%95
     */
    protected static function getLuhnCode($uuid)
    {
        $sum = 0;
        for ($i = 0; $i < strlen($uuid); $i++) {
            $value = (int)$uuid[$i];
            if ($i % 2 == 0) {
                $sum += $value;
            } else {
                $k = $value * 2;
                if ($k > 9) {
                    $k = floor($k / 10) + $k % 10;
                }
                $sum += $k;
            }
        }
        return $sum % 10;
    }
}
