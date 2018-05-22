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
        ],
        'userInfoArr'=>[
            'type'=>MappingType::HAS_MANY,
            'class'=>UserInfoModel::class,
            'foreign_key'=>'user_id',
        ]
    ];

}