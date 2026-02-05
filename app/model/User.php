<?php

namespace app\model;

use think\Model;
use think\facade\Db;

class User extends Model
{
    // 表名
    protected $name = 'user';
    
    // 主键
    protected $pk = 'id';
    
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    
    // 字段列表
    protected $schema = [
        'id' => 'int',
        'username' => 'string',
        'password' => 'string',
        'ccal' => 'string',
        'now' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // 查找用户通过CCAL
    public static function findByCcal($ccal)
    {
        return self::where('ccal', $ccal)->find();
    }
    
    // 查找用户通过用户名
    public static function findByUsername($username)
    {
        return self::where('username', $username)->find();
    }
    
    // 生成唯一的CCAL
    public static function generateCcal()
    {
        do {
            $ccal = mt_rand(100000, 999999);
            $exists = self::where('ccal', $ccal)->find();
        } while ($exists);
        
        return $ccal;
    }
}
