<?php

namespace app\model;

use think\Model;

class Friend extends Model
{
    // 表名
    protected $name = 'friend';
    
    // 主键
    protected $pk = 'id';
    
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    
    // 字段列表
    protected $schema = [
        'id' => 'int',
        'user_ccal' => 'string',
        'friend_ccal' => 'string',
        'status' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // 发送好友请求
    public static function sendFriendRequest($userCcal, $friendCcal)
    {
        // 检查是否已经是好友
        $existing = self::where(function($query) use ($userCcal, $friendCcal) {
            $query->where('user_ccal', $userCcal)->where('friend_ccal', $friendCcal);
        })->whereOr(function($query) use ($userCcal, $friendCcal) {
            $query->where('user_ccal', $friendCcal)->where('friend_ccal', $userCcal);
        })->find();
        
        if ($existing) {
            return false;
        }
        
        // 创建好友请求
        $friend = new self();
        $friend->user_ccal = $userCcal;
        $friend->friend_ccal = $friendCcal;
        $friend->status = 0; // 0: 待确认
        
        return $friend->save();
    }
    
    // 确认好友请求
    public static function confirmFriendRequest($userCcal, $friendCcal)
    {
        // 查找好友请求
        $friend = self::where('user_ccal', $friendCcal)->where('friend_ccal', $userCcal)->where('status', 0)->find();
        
        if (!$friend) {
            return false;
        }
        
        // 更新状态为已确认
        $friend->status = 1;
        
        if ($friend->save()) {
            // 创建反向好友关系
            $reverseFriend = new self();
            $reverseFriend->user_ccal = $userCcal;
            $reverseFriend->friend_ccal = $friendCcal;
            $reverseFriend->status = 1;
            $reverseFriend->save();
            
            return true;
        }
        
        return false;
    }
    
    // 获取用户的好友列表
    public static function getFriendList($userCcal)
    {
        return self::where('user_ccal', $userCcal)->where('status', 1)->select();
    }
    
    // 删除好友
    public static function deleteFriend($userCcal, $friendCcal)
    {
        // 删除正向好友关系
        $result1 = self::where('user_ccal', $userCcal)->where('friend_ccal', $friendCcal)->delete();
        
        // 删除反向好友关系
        $result2 = self::where('user_ccal', $friendCcal)->where('friend_ccal', $userCcal)->delete();
        
        return $result1 || $result2;
    }
    
    // 检查是否是好友
    public static function isFriend($userCcal, $friendCcal)
    {
        $friend = self::where('user_ccal', $userCcal)->where('friend_ccal', $friendCcal)->where('status', 1)->find();
        return $friend ? true : false;
    }
}
