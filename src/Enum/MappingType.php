<?php
/**
 * 表之间的映射关系
 * Author: 闵益飞
 * Date: 2018/5/22
 */

namespace Myf\Enum;


class MappingType
{

    const   HAS_ONE     =   1;
    const   BELONGS_TO  =   2;
    const   HAS_MANY    =   3;
    const   MANY_TO_MANY=   4;

}