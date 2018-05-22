<?php
/**
 * model模型接口
 * Author: 闵益飞
 * Date: 2018/5/22
 */

namespace Myf\Database;


use Myf\Database\Mysql\MysqlDatabase;

abstract class ModelAbstract implements DatabaseInterface
{
    /**
     * @var MysqlDatabase
     */
    protected $database;

    public function __construct(MysqlDatabase $database)
    {
        $this->database = $database;
    }

    /**
     * 查询所有记录
     * @param array ,String $args 查询条件
     * @param Boolean|array|string|int|Double $value 单属性查询值
     * @return mixed
     */
    abstract function selectAll($args=null,$value=false);

    /**
     * 查询一条记录
     * @param array ,String $args 查询条件
     * @param Boolean|array|string|int|Double $value 单属性查询值
     * @return mixed
     */
    abstract function find($args=null,$value=false);

    /**
     * 根据主键查询
     * @param int $id 主键
     * @return mixed
     */
    abstract function findById($id);


    /**
     * 根据主键删除记录
     * @param int $id 主键
     * @return mixed
     */
    abstract function deleteById($id);


    /**
     * 根据id更新内容
     * @param int $id 主键
     * @param array $data 更新的数据
     * @return mixed
     */
    abstract function updateById($id,$data);
}