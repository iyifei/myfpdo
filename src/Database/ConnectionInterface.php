<?php
/**
 * 连接接口
 * Author: 闵益飞
 * Date: 2018/5/21
 */

namespace Myf\Database;


interface ConnectionInterface
{

    /**
     * 设置配置
     * @param $dbConfig
     * @return mixed
     */
    public static function setDbConfig($dbConfig);

    /**
     * 读取配置
     * @return mixed
     */
    public static function getDbConfig();

    /**
     * 获取连接
     * @param null $connName
     * @return mixed
     */
    public static function getConnect($connName=null);

    /**
     * 创建连接
     * @param String $connName 连接名称
     * @return mixed
     */
    public static function createConnect($connName);


    /**
     * 所有连接开启事务
     * @return mixed
     */
    public static function begin();

    /**
     * 所有连接回滚事务
     * @return mixed
     */
    public static function rollback();

    /**
     * 所有连接提交事务
     * @return mixed
     */
    public static function commit();
}