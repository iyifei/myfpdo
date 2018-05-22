<?php
/**
 * Mysql数据库操作接口
 * Author: 闵益飞
 * Date: 2018/5/21
 */

namespace Myf\Database\Mysql;


use Myf\Database\DatabaseInterface;
use Myf\Enum\ActionType;
use Myf\Exception\ErrorCode;
use Myf\Exception\MysqlException;
use Myf\Libs\Log;
use Myf\Libs\Utils;

class MysqlDatabase implements DatabaseInterface
{

    /**
     * @var String 连接
     */
    protected $connection;

    /**
     * 当前操作的数据库
     * @var
     */
    protected $databaseName;

    /**
     * 查询条件
     * @var array
     */
    protected $optionArr = [];

    /**
     * 当前操作表名称
     * @var string
     */
    protected $table = '';

    /**
     * 添加数据的绑定参数前缀
     * @var string
     */
    protected static $addPrefix = 'MyfPdo_ADD_';

    /**
     * 更新数据的绑定参数前缀
     * @var string
     */
    protected static $updatePrefix = 'MyfPdo_UPDATE_';


    /**
     * 构造函数
     * MysqlDatabase constructor.
     * @param array $dbConfig 数据库配置
     */
    public function __construct($dbConfig)
    {
        MysqlConnect::setDbConfig($dbConfig);
        $databaseName = $dbConfig['database'];
        $this->connection = MysqlConnect::getConnect($databaseName);
        $this->databaseName = $this->connection->getDatabase();
    }

    /**
     * 设置表名称
     * @param String $name 表名称
     * @return $this
     */
    public function table($name)
    {
        $this->table = $this->connection->getTablePrefix() . $name;
        $this->optionArr['table'] = $this->table;
        return $this;
    }

    /**
     * 查询一条记录
     * @return array|null
     * @throws MysqlException
     */
    public function findFirst()
    {
        return $this->findAll(false);
    }

    /**
     * 返回查询记录
     * @param bool $allRow true-返回所有记录，false-返回一条记录
     * @return array|null
     * @throws MysqlException
     */
    public function findAll($allRow = true)
    {
        //字段
        if (!isset($this->optionArr['field'])) {
            $this->optionArr['field'] = '*';
        }
        //sql语句
        $sql = sprintf("SELECT %s FROM `%s`", $this->optionArr['field'], $this->table);
        //查询条件
        if (isset($this->optionArr['where'])) {
            $sql .= sprintf(" WHERE %s", $this->optionArr['where']);
        }
        //order by
        if (isset($this->optionArr['order'])) {
            $sql .= sprintf(" ORDER BY %s", $this->optionArr['order']);
        }
        //limit
        if (isset($this->optionArr['limit'])) {
            $sql .= sprintf(" LIMIT %s", $this->optionArr['limit']);
        }
        //参数绑定
        if (!isset($this->optionArr['bindArr'])) {
            $this->optionArr['bindArr'] = [];
        }
        if ($allRow) {
            $action = ActionType::SELECT_ALL;
        } else {
            $action = ActionType::SELECT;
        }
        return $this->execute($sql, $this->optionArr['bindArr'], $action);
    }

    /**
     *  sql查询，返回查询结果
     * @param String $sql
     * @param array $bindArr
     * @return array|null
     * @throws MysqlException
     */
    public function findAllBySql($sql, $bindArr = [])
    {
        return $this->execute($sql, $bindArr, ActionType::SELECT_ALL);
    }

    /**
     * sql查询一条语句
     * @param String $sql
     * @param array $bindArr
     * @return array|null
     * @throws MysqlException
     */
    public function findFirstBySql($sql, $bindArr = [])
    {
        return $this->execute($sql, $bindArr, ActionType::SELECT);
    }

    /**
     * 查询记录条数
     * @return int
     * @throws MysqlException
     */
    public function count()
    {
        $sql = sprintf("SELECT COUNT(*) FROM `%s` ", $this->table);
        if (isset($this->optionArr['where'])) {
            $sql .= sprintf(" WHERE %s", $this->optionArr['where']);
        }
        if (!isset($this->optionArr['bindArr'])) {
            $this->optionArr['bindArr'] = [];
        }
        $row = $this->execute($sql, $this->optionArr['bindArr'], ActionType::SELECT);
        return intval(current($row));
    }

    /**
     * sql查询记录条数
     * @param String $sql sql语句
     * @param array $bindArr 绑定参数
     * @return int
     * @throws MysqlException
     */
    public function countBySql($sql, $bindArr = [])
    {
        $row = $this->execute($sql, $bindArr, ActionType::SELECT);
        return intval(current($row));
    }

    /**
     * * 添加数据
     * @param array $data 表字段对应的key-value数组
     * @return int|mixed 主键id
     * @throws MysqlException
     */
    public function add($data)
    {
        if (!is_array($data)) {
            MysqlException::throeExp(ErrorCode::MYSQL_PARAM_DATA_ERROR, 'mysql add param error');
        }
        $sql = sprintf("INSERT INTO `%s` ", $this->table);
        $fields = $values = $bindArr = [];
        foreach ($data as $field => $value) {
            $bindKey = ":" . self::$addPrefix . $field;
            $fields[] = "`{$field}`";
            $values[] = $bindKey;
            $bindArr[$bindKey] = $value;
        }
        $field = join(',', $fields);
        $value = join(',', $values);
        unset($fields, $values);
        $sql .= sprintf("(%s) VALUES (%s)", $field, $value);
        return $this->execute($sql, $bindArr, ActionType::INSERT);
    }

    /**
     * * 更新数据
     * @param array $data 表字段对应的key-value数组
     * @param String $where 更新条件
     * @param array $bindArr 更新条件的绑定值
     * @return int|mixed
     * @throws MysqlException
     */
    public function update($data, $where, $bindArr = [])
    {
        if (!is_array($data)) {
            MysqlException::throeExp(ErrorCode::MYSQL_PARAM_DATA_ERROR, 'mysql update param error');
        }
        $values = array();
        $sql = sprintf("UPDATE `%s` SET ", $this->table);
        foreach ($data as $key => $val) {
            $bindKey = ":" . self::$updatePrefix . $key;
            $values[] = sprintf("`%s`.`%s`=%s", $this->table, $key, $bindKey);
            $bindArr[$bindKey] = $val;
        }
        $value = join(',', $values);
        unset($values);
        $sql .= $value;
        if (isset($where)) {
            $sql .= sprintf(' WHERE %s ', $where);
        }
        return $this->execute($sql, $bindArr, ActionType::UPDATE);
    }

    /**
     * 删除记录
     * @return int|mixed 返回影响行数
     * @throws MysqlException
     */
    public function delete()
    {
        $sql = sprintf("DELETE FROM `%s`", $this->table);
        if (isset($this->optionArr['where'])) {
            $sql .= sprintf(" WHERE %s", $this->optionArr['where']);
        }
        if (!isset($this->optionArr['bindArr'])) {
            $this->optionArr['bindArr'] = [];
        }
        return $this->execute($sql, $this->optionArr['bindArr'], ActionType::DELETE);
    }

    /**
     * 条件绑定值
     * @param array $bindArr 绑定参数
     * @return \Myf\Database\Mysql\MysqlDatabase
     */
    public function bind($bindArr)
    {
        if (is_array($bindArr)) {
            $this->optionArr['bindArr'] = $bindArr;
        }
        return $this;
    }

    /**
     * 读取表的主键
     * @return mixed|null
     * @throws MysqlException
     */
    public function findPk()
    {
        $sql = sprintf("select column_name from INFORMATION_SCHEMA.KEY_COLUMN_USAGE where constraint_name='PRIMARY' AND table_name='%s' and table_schema='%s'", $this->table, $this->databaseName);
        $row = $this->findFirstBySql($sql);
        return $row['column_name'];
    }

    /**
     * 获取表的所有字段,返回key为字段名称，value为字段的类型
     * @return array|mixed
     * @throws MysqlException
     */
    public function findColumns()
    {
        $sql = sprintf("select column_name,data_type from INFORMATION_SCHEMA.Columns where table_name='%s' and table_schema='%s'", $this->table, $this->databaseName);
        $rows = $this->findAllBySql($sql);
        //$map = array_column($rows, 'DATA_TYPE', 'COLUMN_NAME');
        return $rows;
    }

    /**
     * 查询条件，仅能配合 findAll,findFirst,delete,count 使用
     * @param string $conditions 查询条件
     * @param array $bindArr 绑定参数
     * @return $this
     */
    public function where($conditions, $bindArr = [])
    {
        $this->optionArr['where'] = $conditions;
        if(!empty($bindArr)){
            $this->optionArr['bindArr'] = $bindArr;
        }
        return $this;
    }

    /**
     * 查询那些字段，仅能配合 findAll,findFirst 使用
     * @param array|string $fields 字段集合或字段
     * @return $this
     */
    public function field($fields)
    {
        if (is_string($fields)) {
            $this->optionArr['field'] = $fields;
        } elseif (is_array($fields)) {
            $this->optionArr['field'] = join(',', $fields);
        }
        return $this;
    }

    /**
     * 限制返回记录条数及开始记录数，仅能配合 findAll,findFirst 使用
     * @param int $start 开始记录
     * @param null|int $size 返回记录数
     * @return $this
     */
    public function limit($start, $size = null)
    {
        if (isset($size)) {
            $this->optionArr['limit'] = sprintf("%d,%d", $start, $size);
        } else {
            $this->optionArr['limit'] = $start;
        }
        return $this;
    }

    /**
     * 排序，如：id desc,仅能配合findAll,findFirst使用
     * @param string $orderBy 排序
     * @return $this
     */
    public function orderBy($orderBy)
    {
        $this->optionArr['order'] = $orderBy;
        return $this;
    }


    /**
     * 开启事务
     * @return mixed
     */
    public function begin()
    {
        Log::debug(sprintf("begin transaction id=【%s】", $this->connection->getId()));
        return $this->connection->beginTransaction();
    }

    /**
     * 提交事务
     * @return mixed
     */
    public function commit()
    {
        Log::debug(sprintf("commit transaction id=【%s】", $this->connection->getId()));
        return $this->connection->commit();
    }

    /**
     * 事务回滚
     * @return mixed
     */
    public function rollback()
    {
        Log::debug(sprintf("rollback transaction id=【%s】", $this->connection->getId()));
        return $this->connection->rollBack();
    }

    /**
     * 执行全局sql
     * @param string $sql sql语句
     * @param array $bindArr 绑定参数
     * @param string $action 执行参数
     * @return array|int|mixed|null|string
     * @throws MysqlException
     */
    protected function execute($sql, $bindArr = [], $action)
    {
        $sqlStartTime = Utils::getMillisecond();
        $stmt = $this->connection->prepare($sql);
        Log::debug(sprintf("start execute sql=【%s】，bind=【%s】", $sql, json_encode($bindArr)));
        $exeRes = $stmt->execute($bindArr);
        $res = null;
        switch ($action) {
            case ActionType::SELECT:
                $res = $stmt->fetch(MysqlPDO::FETCH_ASSOC);
                break;
            case ActionType::SELECT_ALL:
                //返回所有结果集
                $res = $stmt->fetchAll(MysqlPDO::FETCH_ASSOC);
                break;
            case ActionType::UPDATE:
            case ActionType::DELETE:
            case ActionType::COUNT:
                $res = $stmt->rowCount();
                break;
            case ActionType::INSERT:
                //返回主键
                $res = $this->connection->lastInsertId();
                break;
        }
        //重置查询条件
        $this->optionArr = [];
        $sqlEndTime = Utils::getMillisecond();
        Log::debug(sprintf("end execute ct=【%sms】，ec=【%s】，conn=【%s】，sql=【%s】，bind=【%s】",
            ($sqlEndTime - $sqlStartTime), $exeRes, $this->connection->getId(), $sql, json_encode($bindArr)));
        if (!$exeRes) {
            $errorInfo = json_encode($stmt->errorInfo());
            Log::error(sprintf("execute Error=【%s】", $errorInfo));
            MysqlException::throeExp(ErrorCode::MYSQL_SQL_ERROR, $errorInfo);
        }
        return $res;
    }

    /**
     * 获取表名称
     * @return mixed
     */
    public function getTableName()
    {
        return $this->table;
    }

    /**
     * 获取数据库名称
     * @return mixed
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }
}