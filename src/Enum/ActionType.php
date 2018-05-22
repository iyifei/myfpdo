<?php
/**
 * 执行sql action的类型
 * Author: 闵益飞
 * Date: 2018/5/22
 */

namespace Myf\Enum;


class ActionType
{
    //查询单条记录
    const SELECT = 'select';
    //查询所有记录
    const SELECT_ALL = 'selectALL';
    //更新
    const UPDATE = 'update';
    //删除
    const DELETE = 'delete';
    //插入
    const INSERT = 'insert';
    //统计
    const COUNT = 'count';

}