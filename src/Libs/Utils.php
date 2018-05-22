<?php
/**
 * 工具类
 * Author: 闵益飞
 * Date: 2018/5/22
 */

namespace Myf\Libs;


class Utils
{

    /**
     * 获取当前时间戳-精确到毫秒
     * @return float
     */
    public static function getMillisecond()
    {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    /**
     * 获取唯一id
     * @return string
     */
    public static function getUUID(){
        $randStr = uniqid(mt_rand(), true) . self::getMillisecond();
        $uuid = md5($randStr);
        return $uuid;
    }

    /**
     * 驼峰命名转下划线命名，如 UserName => user_name
     * @param string $s
     * @return string
     */
    public static function toUnderLineName($s) {
        $s = lcfirst($s);
        $chars = str_split($s);
        $res = "";
        foreach ($chars as $c) {
            if (self::isCapitalLetter($c)) {
                $c = "_" . strtolower($c);
            }
            $res .= $c;
        }
        return $res;
    }

    /**
     * 判断字符串是否为大写字母
     * @param type $c
     * @return boolean
     */
    public static function isCapitalLetter($c) {
        if (preg_match('/^[A-Z]+$/', $c)) {
            return true;
        } else {
            return false;
        }
    }
}