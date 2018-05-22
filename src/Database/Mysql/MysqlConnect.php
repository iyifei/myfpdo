<?php
/**
 * Mysql连接
 * Author: 闵益飞
 * Date: 2018/5/21
 */

namespace Myf\Database\Mysql;


use Myf\Database\ConnectionInterface;
use Myf\Exception\ErrorCode;
use Myf\Exception\MysqlException;
use Myf\Libs\Log;
use Myf\Libs\Utils;

class MysqlConnect implements ConnectionInterface
{

    /**
     * 连接集合
     * @var array
     */
    protected static $connectMap=[];

    /**
     * 数据库配置
     * @var array
     */
    protected static $dbConfig;


    /**
     * 获取连接
     * @param null $connName
     * @return mixed|MysqlPDO
     */
    public static function getConnect($connName = null)
    {
        if(isset(self::$connectMap[$connName])){
            $connect = self::$connectMap[$connName];
        }else{
            $connect = self::createConnect($connName);
            self::$connectMap[$connName]=$connect;
        }
        return $connect;
    }

    /**
     * 创建连接
     * @param String $connName
     * @return mixed|MysqlPDO
     */
    public static function createConnect($connName)
    {
        $startTime = Utils::getMillisecond();
        $db = self::$dbConfig;
        $host = $db["host"];
        $user = $db["user"];
        $password = $db["password"];
        $port = $db["port"];
        $database = $db["database"];
        $charset = isset($db["charset"])?$db['charset']:'utf8';
        $prefix = isset($db['prefix'])?$db['prefix']:'';
        $dsn = sprintf("mysql:host=%s;dbname=%s;port=%d;charset=%s", $host, $database, $port, $charset);
        $pdo = new MysqlPDO($dsn, $user, $password);
        $pdo->setDatabase($database);
        $pdo->setTablePrefix($prefix);
        $uuid = Utils::getUUID();
        $pdo->setId($uuid);
        Log::debug(sprintf("mysqlPdo costTime=【%s】， dsn=【%s】，id=【%s】，user=【%s】，password=【%s】",(Utils::getMillisecond()-$startTime),$dsn,$uuid,$user,$password));
        return $pdo;
    }

    /**
     * 所有连接开启事务
     * @return mixed
     */
    public static function begin()
    {

        foreach (self::$connectMap as $conn){
            /**
             * @var MysqlPDO $conn;
             */
            $conn->beginTransaction();
        }
    }

    /**
     * 所有连接回滚事务
     * @return mixed
     */
    public static function rollback()
    {
        foreach (self::$connectMap as $conn){
            /**
             * @var MysqlPDO $conn;
             */
            $conn->rollBack();
        }
    }

    /**
     * 所有连接提交事务
     * @return mixed
     */
    public static function commit()
    {
        foreach (self::$connectMap as $conn){
            /**
             * @var MysqlPDO $conn;
             */
            $conn->commit();
        }
    }

    /**
     * 设置配置
     * @param $dbConfig
     * @return mixed
     */
    public static function setDbConfig($dbConfig)
    {
        self::$dbConfig = $dbConfig;
    }

    /**
     * 读取配置
     * @return mixed
     */
    public static function getDbConfig()
    {
        return self::$dbConfig;
    }
}