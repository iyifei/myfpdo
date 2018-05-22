<?php
/**
 * 数据库操作接口
 * Author: 闵益飞
 * Date: 2018/5/21
 */

namespace Myf\Database;


interface DatabaseInterface
{
    /**
     * 设置表名称
     * @param String $name 表名称
     * @return mixed
     */
    public function table($name);

    /**
     * 获取连接名称
     * @return mixed
     */
    public function getDatabaseName();

    /**
     * 查询一条记录
     * @return mixed
     */
    public function findFirst();

    /**
     * 返回查询记录
     * @param bool $allRow true-返回所有记录，false-返回一条记录
     * @return mixed
     */
    public function findAll($allRow=true);

    /**
     * sql查询，返回查询结果
     * @param String $sql sql语句
     * @param array $bindArr 绑定参数
     * @return mixed
     */
    public function findAllBySql($sql,$bindArr=[]);

    /**
     * sql查询一条语句，
     * @param String $sql sql语句
     * @param array $bindArr 绑定参数
     * @return mixed
     */
    public function findFirstBySql($sql,$bindArr=[]);


    /**
     * 查询记录条数
     * @return mixed
     */
    public function count();


    /**
     * sql查询记录条数
     * @param String $sql sql语句
     * @param array $bindArr 绑定参数
     * @return mixed
     */
    public function countBySql($sql,$bindArr=[]);


    /**
     * 添加数据
     * @param array $data 表字段对应的key-value数组
     * @return mixed 返回新主键
     */
    public function add($data);

    /**
     * 更新数据
     * @param array $data 表字段对应的key-value数组
     * @param String $where 更新条件
     * @param array $bindArr 更新条件的绑定值
     * @return mixed
     */
    public function update($data,$where,$bindArr=[]);


    /**
     * 删除记录
     * @return mixed
     */
    public function delete();


    /**
     * 条件绑定值
     * @param array $bindArr 绑定参数
     * @return mixed
     */
    public function bind($bindArr);

    /**
     * 读取表的主键
     * @return mixed
     */
    public function findPk();

    /**
     * 获取表的所有字段,返回key为字段名称，value为字段的类型
     * @return mixed
     */
    public function findColumns();

    /**
     * 查询条件，仅能配合 findAll,findFirst,delete,count 使用
     * @param $conditions
     * @param array $bindArr
     * @return mixed
     */
    public function where($conditions,$bindArr=[]);

    /**
     * 查询那些字段，仅能配合 findAll,findFirst 使用
     * @param $fields
     * @return mixed
     */
    public function field($fields);

    /**
     * 限制返回记录条数及开始记录数，仅能配合 findAll,findFirst 使用
     * @param int $start 开始记录
     * @param null $size 返回记录数
     * @return mixed
     */
    public function limit($start,$size=null);

    /**
     * 排序，如：id desc,仅能配合findAll,findFirst使用
     * @param $orderBy
     * @return mixed
     */
    public function orderBy($orderBy);


    /**
     * 开启事务
     * @return mixed
     */
    public function begin();

    /**
     * 提交事务
     * @return mixed
     */
    public function commit();


    /**
     * 事务回滚
     * @return mixed
     */
    public function rollback();

}