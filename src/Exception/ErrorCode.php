<?php
/**
 * 错误代码
 * Author: 闵益飞
 * Date: 2018/5/22
 */

namespace Myf\Exception;


class ErrorCode
{

    /**
     * 系统级错误
     */
    const SYS_ERROR = 10001;

    /**
     * 配置文件错误
     */
    const MYSQL_CONFIG_ERROR = 10002;

    /**
     * mysql参数错误
     */
    const MYSQL_PARAM_DATA_ERROR = 10003;

    /**
     * sql解析错误
     */
    const MYSQL_SQL_ERROR = 10004;
}