<?php
/**
 * MysqlConnect 单元测试
 * Author: 闵益飞
 * Date: 2018/5/22
 */

namespace Tests\Database\Mysql;


use Myf\Database\Mysql\MysqlConnect;
use Myf\Exception\MysqlException;
use Myf\Libs\Log;
use PHPUnit\Framework\TestCase;
use Tests\Model\UserModel;

class MysqlConnectTest extends TestCase
{

    private function getConfig(){
        $config = [
            'database' => array(
                'test' => array(
                    'host' => 'localhost',
                    'port' => '3306',
                    'user' => 'root',
                    'password' => 'minyifei.cn',
                    'database' => 'test',
                    'charset' => 'utf8',
                    'prefix' => ''
                ),
            ),
            'default'=>'test',
        ];
        return $config;
    }

    private function getConnect(){
        $config = $this->getConfig();
        $databaseName = $config['default'];
        MysqlConnect::setDbConfig($config['database'][$databaseName]);
        return MysqlConnect::getConnect($databaseName);
    }

    public function testGetConnect(){
        $connect = $this->getConnect();
        $this->assertNotEmpty($connect->getId());
    }

    public function testGetDbConfig(){
        $connect = $this->getConnect();
        $databaseName = $connect->getDatabase();
        $config =  MysqlConnect::getDbConfig();
        $this->assertEquals($databaseName,$config['database']);
    }

    public function testTrans(){
        $userModel = new UserModel();
        MysqlConnect::begin();
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

            MysqlConnect::commit();
        }catch (MysqlException $e){
            MysqlConnect::rollback();
        }
    }

    public function testRollback(){
        $userModel = new UserModel();
        MysqlConnect::begin();
        try{
            $addData = [
                'name'=>'test'.rand(100,10000),
                'create_time'=>date("Y-m-d H:i:s"),
            ];
            $id = $userModel->add($addData);
            //抛出异常
            MysqlException::throeExp(1,'test mysqlConnect rollback');
            MysqlConnect::commit();
        }catch (MysqlException $e){
            MysqlConnect::rollback();
            Log::info('mysqlConnect rollback');
        }

        $row = $userModel->findById($id);
        $this->assertEmpty($row);
    }

}