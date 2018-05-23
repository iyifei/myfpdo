# myfpdo
此组件特点：
1. 需要简单的类库php操作mysql数据库
2. 封装了个简单的Model模型，上手快速
3. 封装了mysql pdo各种方法，有效的防止数据库注入

## 项目安装
```bash
composer require iyifei/myfpdo
```

## 快速使用

### 1、首选需要定义一个全局常量【MYF_PDO_LOG_PATH】用于存放log日志【MYF_PDO_LOG_LEVEL】用于定义全局log级别，其次继承MysqlModel创建一个自己的Model实现 getDbConfig()方法，示例代码如下(Model.php)：
```php
bootstrap.php
<?php
/**
 * tests启动类
 * Author: 闵益飞
 * Date: 2018/5/22
 */

use Monolog\Logger;

define('APP_PATH',dirname(dirname(__FILE__)));
require APP_PATH."/vendor/autoload.php";

//日志生成目录
define('MYF_PDO_LOG_PATH',APP_PATH."/report/logs");
//日志级别
define('MYF_PDO_LOG_LEVEL',Logger::DEBUG);

Model.php
<?php
/**
 * model基类
 * Author: 闵益飞
 * Date: 2018/5/22
 */

namespace Tests\Model;


use Myf\Database\Mysql\MysqlModel;

class Model extends MysqlModel
{

    /**
     * 获取数据库配置文件
     * @return mixed
     */
    public function getDbConfig()
    {
        //Todo 此配置可以通过配置文件加载
        return array(
            //数据库连接
            'host' => 'localhost',
            //数据库端口
            'port' => '3306',
            //数据库用户名
            'user' => 'root',
            //数据库密码
            'password' => 'minyifei.cn',
            //数据库名
            'database' => 'test',
            //编码字符
            'charset' => 'utf8',
            //前缀
            'prefix' => ''
        );
    }

}

```

### 2、在test库中创建两个表，sql如下：
```sql

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增长',
  `name` varchar(50) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
--  Records of `user`
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES ('1', 'myf', '2018-05-22 14:45:55'),('2', 'myf2', '2018-05-22 14:45:56');
COMMIT;


CREATE TABLE `user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增长',
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL COMMENT '姓名',
  `age` int(11) DEFAULT '0' COMMENT '年龄',
  `home_town` varchar(50) DEFAULT '' COMMENT '家乡',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `user_info`
-- ----------------------------
BEGIN;
INSERT INTO `user_info` VALUES ('1', '1', 'myf_106', '8824', '家乡_8824'), ('2', '2', 'myf4383', '4383', '家乡_4383');
COMMIT;

```

### 3、创建两个类UserModel和UserInfoModel集成Model，代码如下：

UserModel.php
```php
<?php
/**
 * User表orm操作类
 * Author: 闵益飞
 * Date: 2018/5/22
 */

namespace Tests\Model;

use Myf\Enum\MappingType;

class UserModel extends Model
{

    protected $linkMap = [
        'userInfo'=>[
            'type'=>MappingType::HAS_ONE,
            'class'=>UserInfoModel::class,
            'foreign_key'=>'user_id',
            'key'=>'id',
        ],
        'userInfoArr'=>[
            'type'=>MappingType::HAS_MANY,
            'class'=>UserInfoModel::class,
            'foreign_key'=>'user_id',
            'key'=>'id',
        ]
    ];

}
```
UserInfoModel.php

```php
<?php
/**
 * 用户详细信息
 * Author: 闵益飞
 * Date: 2018/5/22
 */

namespace Tests\Model;


class UserInfoModel extends Model
{

}
```

### 4、使用UserModel查询数据示例,MysqlModelTest.php
```php
<?php
/**
 * 测试MysqlModel
 * Author: 闵益飞
 * Date: 2018/5/22
 */

namespace Tests\Database\Mysql;


use Myf\Enum\ActionType;
use Myf\Exception\ErrorCode;
use Myf\Exception\MysqlException;
use Myf\Libs\Log;
use PHPUnit\Framework\TestCase;
use Tests\Model\UserModel;

class MysqlModelTest extends TestCase
{
    public function testLink(){
        $id = 1;
        $userModel = new UserModel();
        $user = $userModel
            ->link(['userInfo','userInfoArr'])
            ->findById($id);

        $this->assertEquals($user['userInfo']['user_id'],$id);
        $this->assertEquals($user['userInfoArr'][0]['user_id'],$id);

        $users = $userModel->link('userInfo')->findAll();
        foreach ($users as $user){
            $this->assertEquals($user['userInfo']['user_id'],$user['id']);
        }
    }

    public function testFindFirst(){
        $userModel = new UserModel();
        //查询id=1
        $user = $userModel->orderBy('id asc')->findFirst();
        //查询id=2
        $where = 'id=2';
        $user2 = $userModel->where($where)->findFirst();

        $this->assertNotEquals($user['id'],$user2['id']);
    }

    public function testFindAll(){
        $userModel = new UserModel();
        $rows = $userModel->findAll();
        $this->assertEquals(count($rows),2);

        //查询ID=1
        $row = $userModel->orderBy('id asc')->findAll(false);//相当于findFirst
        $this->assertEquals($row['id'],1);
    }


    public function testFindAllBySql(){
        $userModel = new UserModel();
        $sql = 'select * from user where id=1';
        $rows = $userModel->findAllBySql($sql);
        $this->assertEquals(count($rows),1);
        //获取主键=1
        $this->assertEquals(current($rows)['id'],1);
    }

    public function testFindFirstBySql(){
        $userModel = new UserModel();
        $id = 1;
        $sql = 'select * from user where id='.$id;
        $row = $userModel->findFirstBySql($sql);

        $sql2 = "select * from user where id=:id";
        $bindArr = ['id'=>$id];
        $row2 = $userModel->findFirstBySql($sql2,$bindArr);
        $this->assertEquals($row['id'],$row2['id']);
    }


    public function testCount(){
        $userModel = new UserModel();
        $count = $userModel->count();
        $this->assertEquals($count,2);

        $where ='id=1';
        $count = $userModel->where($where)->count();
        $this->assertEquals($count,1);
    }

    public function testCountBySql(){
        $userModel = new UserModel();
        $sql = "select count(*) from user ";
        $count = $userModel->countBySql($sql);
        $this->assertEquals($count,2);

        $sql = 'select count(*) from user where id=:id';
        $bindArr = ['id'=>2];
        $count = $userModel->countBySql($sql,$bindArr);
        $this->assertEquals($count,1);
    }

    public function testAddDelete(){
        $userModel = new UserModel();
        $userModel->begin();
        try{
            $name = "test".rand(1,1000);
            $data = [
                'name'=>$name,
                'create_time'=>date('Y-m-d H:i:s'),
            ];
            $id = $userModel->add($data);

            $row = $userModel->findById($id);
            $this->assertEquals($row['name'],$name);

            $rowCount = $userModel->deleteById($id);
            $this->assertEquals($rowCount,1);

            $userModel->commit();
        }catch (MysqlException $ex){
            $userModel->rollback();
        }

        try{
            $userModel->add('abc');
        }catch (MysqlException $e){
            $this->assertEquals($e->getCode(),ErrorCode::MYSQL_PARAM_DATA_ERROR);
        }
    }

    public function testUpdate(){
        $id = 1;
        $name = "test".rand(1,1000);
        $data = [
            'name'=>$name
        ];
        $userModel = new UserModel();
        $where = sprintf('id=%d',$id);
        $rowCount = $userModel->update($data,$where);
        $this->assertEquals($rowCount,1);
        $row = $userModel->findById($id);
        $this->assertEquals($row['name'],$name);

        $name = "test".rand(1,1000);
        $data = [
            'name'=>$name
        ];
        $rowCount = $userModel->updateById($id,$data);
        $this->assertEquals($rowCount,1);

        $row = $userModel->findById($id);
        $this->assertEquals($row['name'],$name);

        $newName = "test".rand(1,1000);
        $where = 'name=:name';
        $data = [
            'name'=>$newName
        ];
        $bindArr = ['name'=>$name];
        $rowCount = $userModel->update($data,$where,$bindArr);
        $this->assertEquals($rowCount,1);

        try{
            $userModel->update('abc','id=1');
        }catch (MysqlException $e){
            $this->assertEquals($e->getCode(),ErrorCode::MYSQL_PARAM_DATA_ERROR);
        }

    }

    public function testBindWhere(){
        $where = 'id=:id';
        $id = 2;
        $bind=['id'=>$id];
        $userModel = new UserModel();
        $row = $userModel->where($where)->bind($bind)->findFirst();
        $this->assertEquals($row['id'],$id);

        $row = $userModel->where($where,$bind)->findFirst();
        $this->assertEquals($row['id'],$id);
    }

    public function testFindPk(){
        $pk = 'id';
        $userModel = new UserModel();
        $queryPk = $userModel->findPk();
        $this->assertEquals($pk,$queryPk);
    }

    public function testFindColumns(){
        $columns = ['id','name','create_time'];
        $userModel = new UserModel();
        $queryColumns = $userModel->findColumns();
        foreach ($queryColumns as $column){
            $this->assertTrue(in_array($column['column_name'],$columns));
        }
    }

    public function testField(){
        $id = 1;
        $fields = ['id','name'];
        $userModel = new UserModel();
        $row = $userModel->field('id,name')->findById($id);
        foreach ($row as $field=>$val){
            $this->assertTrue(in_array($field,$fields));
        }

        $row = $userModel->field($fields)->findById($id);
        foreach ($row as $field=>$val){
            $this->assertTrue(in_array($field,$fields));
        }
    }

    public function testLimitOrderBy(){
        $userModel = new UserModel();
        $rows = $userModel->orderBy('id desc')->limit(1,2)->findAll();
        $this->assertEquals(current($rows)['id'],1);

        $rows = $userModel->orderBy('id desc')->limit(1)->findAll();
        $this->assertEquals(current($rows)['id'],2);

    }

    public function testRollback(){
        $userModel = new UserModel();
        $userModel->begin();
        try{
            $addData = [
                'name'=>'test'.rand(100,10000),
                'create_time'=>date("Y-m-d H:i:s"),
            ];
            $id = $userModel->add($addData);
            //抛出异常
            MysqlException::throeExp(1,'test rollback');
            $userModel->commit();
        }catch (MysqlException $e){
            $userModel->rollback();
            Log::info('rollback');
        }

        $row = $userModel->findById($id);
        $this->assertEmpty($row);
    }

    public function testGetDatabaseName(){
        $name = 'test';
        $userModel = new UserModel();
        $queryName = $userModel->getDatabaseName();
        $this->assertEquals($name,$queryName);
    }

    public function testSelectAll(){
        $userModel = new UserModel();
        $rows = $userModel->selectAll();
        $this->assertEquals(count($rows),2);

        $rows = $userModel->selectAll('id',2);
        $this->assertEquals(current($rows)['id'],2);

        $rows = $userModel->selectAll('id=1');
        $this->assertEquals(current($rows)['id'],1);
    }

    public function testErrorSql(){
        $sql = 'select * from abc0001 where id=1';
        $userModel = new UserModel();
        try{
            $userModel->findAllBySql($sql);
        }catch (MysqlException $e){
            $this->assertEquals($e->getCode(),ErrorCode::MYSQL_SQL_ERROR);
        }
    }

    public function testExecute(){
        $sql = "select * from user ";
        $userModel = new UserModel();
        $rows = $userModel->execute($sql,ActionType::SELECT_ALL);
        $this->assertEquals(count($rows),2);
    }
}
```