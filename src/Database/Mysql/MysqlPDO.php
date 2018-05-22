<?php
/**
 * mysqlPDO
 * Author: 闵益飞
 * Date: 2018/5/22
 */

namespace Myf\Database\Mysql;


class MysqlPDO extends \PDO
{

    /**
     * 当前pdo连接唯一id
     * @var String
     */
    protected $id;

    /**
     * 当前操作的数据库
     * @var String
     */
    protected $database;

    /**
     * 当前操作数据库所有表的前缀
     * @var
     */
    protected $tablePrefix;

    /**
     * @return String
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param String $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * @return mixed
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * @param mixed $tablePrefix
     */
    public function setTablePrefix($tablePrefix)
    {
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * @return String
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param String $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

}