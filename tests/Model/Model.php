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
        return array(
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'root',
            'password' => 'minyifei.cn',
            'database' => 'test',
            'charset' => 'utf8',
            'prefix' => ''
        );
    }

}