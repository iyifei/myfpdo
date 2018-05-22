<?php
/**
 * mysql基类model模型
 * Author: 闵益飞
 * Date: 2018/5/22
 */

namespace Myf\Database\Mysql;


use Myf\Database\ModelAbstract;
use Myf\Libs\Utils;

abstract class MysqlModel extends ModelAbstract
{
    protected $model;
    protected $tableName;
    protected $databaseName;

    public function __construct()
    {
        $this->tableName = $this->getTableName();
        $dbConfig = $this->getDbConfig();
        $database = new MysqlDatabase($dbConfig);
        parent::__construct($database);
        $this->table($this->tableName);
    }

    /**
     * 获取数据库配置文件
     * @return mixed
     */
    abstract public function getDbConfig();

    /**
     * 设置表名称
     * @param String $name 表名称
     * @return $this
     */
    public function table($name)
    {
         $this->database->table($name);
         return $this;
    }

    /**
     * 查询一条记录
     * @return array|mixed|null
     * @throws \Myf\Exception\MysqlException
     */
    public function findFirst()
    {
        return $this->database->findFirst();
    }

    /**
     * 返回查询记录
     * @param bool $allRow
     * @return array|mixed|null
     * @throws \Myf\Exception\MysqlException
     */
    public function findAll($allRow = true)
    {
        return $this->database->findAll($allRow);
    }

    /**
     * sql查询，返回查询结果
     * @param String $sql
     * @param array $bindArr
     * @return array|mixed|null
     * @throws \Myf\Exception\MysqlException
     */
    public function findAllBySql($sql, $bindArr = [])
    {
        return $this->database->findAllBySql($sql,$bindArr);
    }

    /**
     * sql查询一条语句，
     * @param String $sql
     * @param array $bindArr
     * @return array|mixed|null
     * @throws \Myf\Exception\MysqlException
     */
    public function findFirstBySql($sql, $bindArr = [])
    {
        return $this->database->findFirstBySql($sql,$bindArr);
    }

    /**
     * 查询记录条数
     * @return int|mixed
     * @throws \Myf\Exception\MysqlException
     */
    public function count()
    {
        return $this->database->count();
    }

    /**
     * sql查询记录条数
     * @param String $sql
     * @param array $bindArr
     * @return int|mixed
     * @throws \Myf\Exception\MysqlException
     */
    public function countBySql($sql, $bindArr = [])
    {
        return $this->database->countBySql($sql,$bindArr);
    }

    /**
     * 添加数据
     * @param array $data
     * @return int|mixed
     * @throws \Myf\Exception\MysqlException
     */
    public function add($data)
    {
        return $this->database->add($data);
    }

    /**
     * 更新数据
     * @param array $data
     * @param String $where
     * @param array $bindArr
     * @return int|mixed
     * @throws \Myf\Exception\MysqlException
     */
    public function update($data, $where, $bindArr = [])
    {
        return $this->database->update($data,$where,$bindArr);
    }

    /**
     * 根据主键更新内容
     * @param int $id 主键
     * @param array $data
     * @return int|mixed
     */
    public function updateById($id, $data)
    {
        $where = sprintf("id=%d",$id);
        return $this->update($data,$where);
    }

    /**
     * 删除记录
     * @return int|mixed
     * @throws \Myf\Exception\MysqlException
     */
    public function delete()
    {
        return $this->database->delete();
    }

    /**
     * 条件绑定值
     * @param array $bindArr 绑定参数
     * @return $this
     */
    public function bind($bindArr)
    {
         $this->database->bind($bindArr);
         return $this;
    }

    /**
     * 读取表的主键
     * @return mixed|null
     * @throws \Myf\Exception\MysqlException
     */
    public function findPk()
    {
        return $this->database->findPk();
    }

    /**
     *  获取表的所有字段,返回key为字段名称，value为字段的类型
     * @return array|mixed
     * @throws \Myf\Exception\MysqlException
     */
    public function findColumns()
    {
        return $this->database->findColumns();
    }

    /**
     * 查询条件，仅能配合 findAll,findFirst,delete,count 使用
     * @param $conditions
     * @param array $bindArr
     * @return $this
     */
    public function where($conditions, $bindArr = [])
    {
        $this->database->where($conditions,$bindArr);
        return $this;
    }

    /**
     * 查询那些字段，仅能配合 findAll,findFirst 使用
     * @param $fields
     * @return $this
     */
    public function field($fields)
    {
        $this->database->field($fields);
        return $this;
    }

    /**
     * 限制返回记录条数及开始记录数，仅能配合 findAll,findFirst 使用
     * @param int $start 开始记录
     * @param null $size 返回记录数
     * @return $this
     */
    public function limit($start, $size = null)
    {
        $this->database->limit($start,$size);
        return $this;
    }

    /**
     * 排序，如：id desc,仅能配合findAll,findFirst使用
     * @param $orderBy
     * @return $this
     */
    public function orderBy($orderBy)
    {
        $this->database->orderBy($orderBy);
        return $this;
    }

    /**
     * 开启事务
     * @return mixed
     */
    public function begin()
    {
        return $this->database->begin();
    }

    /**
     * 提交事务
     * @return mixed
     */
    public function commit()
    {
        return $this->database->commit();
    }

    /**
     * 事务回滚
     * @return mixed
     */
    public function rollback()
    {
        return $this->database->rollback();
    }

    /**
     * 查询所有记录
     * @param null $args
     * @param bool $value
     * @return array|mixed|null
     * @throws \Myf\Exception\MysqlException
     */
    function selectAll($args = null, $value = false)
    {
        return $this->setOptions($args,$value)->findAll();
    }

    /**
     * 查询一条记录
     * @param null $args
     * @param bool $value
     * @return array|mixed|null
     * @throws \Myf\Exception\MysqlException
     */
    function find($args = null, $value = false)
    {
        return $this->setOptions($args,$value)->findFirst();
    }

    /**
     * 根据主键查询
     * @param int $id
     * @return array|mixed|null
     * @throws \Myf\Exception\MysqlException
     */
    function findById($id)
    {
        return $this->find('id',$id);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function deleteById($id)
    {
        $where = sprintf('id=%d',$id);
        return $this->where($where)->delete();
    }

    /**
     * 设置参数
     * @param $args
     * @param bool $value
     * @return $this
     */
    protected function setOptions($args,$value=false){
        if(gettype($value)!='boolean'){
            $bindKey = sprintf(":%s",$args);
            $where = sprintf("%s=%s",$args,$bindKey);
            $bind = [$bindKey=>$value];
            $this->where($where)->bind($bind);
        }elseif(is_string($args)){
            $this->where($args);
        }
        return $this;
    }

    /**
     * 获取表名称
     * @return mixed
     */
    public function getTableName()
    {
        $className = get_class($this);
        $names = explode("\\", $className);
        $class = array_pop($names);
        $tableName = Utils::toUnderLineName($class);
        //去掉未部的_model,把user_model改为user
        $tableNames = explode("_",$tableName);
        $len = count($tableNames);
        if($tableNames[$len-1]=='model'){
            unset($tableNames[$len-1]);
        }
        $tableName = join("_",$tableNames);
        return $tableName;
    }

    public function getDatabaseName()
    {
        return $this->database->getDatabaseName();
    }

}