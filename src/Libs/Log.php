<?php
/**
 * 日志
 * Author: 闵益飞
 * Date: 2018/5/22
 */

namespace Myf\Libs;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;


/**
 * Class Log
 *
 * @package Myf\Libs
 * @method static log($level, $message, array $context = array())
 * @method static debug($message, array $context = array())
 * @method static info($message, array $context = array())
 * @method static notice($message, array $context = array())
 * @method static warn($message, array $context = array())
 * @method static warning($message, array $context = array())
 * @method static err($message, array $context = array())
 * @method static error($message, array $context = array())
 * @method static crit($message, array $context = array())
 * @method static critical($message, array $context = array())
 * @method static alert($message, array $context = array())
 *
 */
class Log
{

    static $logger;
    static $methods;

    /**
     * @return Logger
     * @throws \Exception
     * @throws \ReflectionException
     */
    public static function getInstance(){
        if(!isset(self::$logger)){
            self::$logger = new Logger('myfpdo');
            $file = sprintf("%s/%s.log",MYF_PDO_LOG_PATH,date("Y-m-d"));
            $streamHandler = new StreamHandler($file);
            self::$logger->pushHandler($streamHandler);

            //反射读取logger的所有方法
            $ref = new \ReflectionClass(Logger::class);
            $methods = $ref->getMethods();
            foreach ($methods as $method){
                self::$methods[]=$method->name;
            }
        }
        return self::$logger;
    }

    /**
     * @param $name
     * @param $arguments
     * @throws \Exception
     * @throws \ReflectionException
     */
    public static function __callStatic($name, $arguments) {

        $logger = self::getInstance();
        if(in_array($name,self::$methods)){
            call_user_func_array([$logger,$name],$arguments);
        }else{
            var_dump($name,$arguments,self::$methods);
        }
    }

}