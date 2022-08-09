<?php

namespace crichain\utils;

use phpseclib\Math\BigInteger;
use Web3\Utils;

class Functions
{

    /**
     * 生成uuid
     *
     * @param string $prefix
     * @return string
     */
    public static function create_uuid(string $prefix = ""): string
    {
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr($chars, 0, 8)
            . substr($chars, 8, 4)
            . substr($chars, 12, 4)
            . substr($chars, 16, 4)
            . substr($chars, 20, 12);
        return $prefix . $uuid;
    }

    /**
     * 生成时间戳（毫秒）
     *
     * @return float
     */
    public static function mtime(): float
    {

        list($mse, $sec) = explode(' ', microtime());

        return (float)sprintf('%.0f', (floatval($mse) + floatval($sec)) * 1000);
    }

    /**
     * 将float转换为交易所需要的amount
     *
     * @param string $num
     * @return string
     */
    public static function getTransferAmountByFloatNum(string $num): string
    {
        $num = bcmul($num, Utils::UNITS['ether']);
        $bn = new BigInteger($num, 10);
        $hexNum = $bn->toHex();
        $binNum = Utils::hexToBin($hexNum);
        return $binNum;
    }


    /**
     * 生成operateId
     *
     * @return string
     */
    public static function createOperateId(): string
    {
        return self::msecdate(self::mtime()) . rand(100, 999) . rand(1000, 9999);
    }

    /**
     * 生成17位毫秒时间
     * @param $time
     * @return string
     */
    public static function msecdate($time): string
    {
        $tag = 'YmdHis';
        $a = substr($time, 0, 10);
        $b = substr($time, 10);
        $date = date($tag, $a) . $b;
        return $date;
    }

}