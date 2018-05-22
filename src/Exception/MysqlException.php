<?php
/**
 * mysql异常
 * Author: 闵益飞
 * Date: 2018/5/22
 */

namespace Myf\Exception;


use Throwable;

class MysqlException extends \RuntimeException
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * 抛出异常
     * @param \Exception $e
     * @throws \Exception
     */
    public static function throwException(\Exception $e){
        throw  new MysqlException($e->getMessage(),$e->getCode());
    }

    /**
     * 抛出异常
     * @param $code
     * @param string $msg
     * @throws MysqlException
     */
    public static function throeExp($code,$msg=''){
        throw  new MysqlException($msg,$code);
    }

}