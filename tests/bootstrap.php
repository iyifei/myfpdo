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
