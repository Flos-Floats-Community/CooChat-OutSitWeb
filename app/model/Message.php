<?php

namespace app\model;

use think\Model;

class Message extends Model
{
    // 表名
    protected $name = 'message';
    
    // 主键
    protected $pk = 'id';
    
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    
    // 字段列表
    protected $schema = [
        'id' => 'int',
        'sender_ccal' => 'string',
        'receiver_ccal' => 'string',
        'content' => 'text',
        'message_type' => 'string',
        'is_read' => 'int',
        'created_at' => 'datetime',
    ];
    
    // 获取两个用户之间的聊天历史
    public static function getHistory($ccal1, $ccal2, $limit = 50, $offset = 0)
    {
        return self::where(function($query) use ($ccal1, $ccal2) {
            $query->where('sender_ccal', $ccal1)->where('receiver_ccal', $ccal2);
        })->whereOr(function($query) use ($ccal1, $ccal2) {
            $query->where('sender_ccal', $ccal2)->where('receiver_ccal', $ccal1);
        })->order('created_at', 'desc')->limit($limit)->offset($offset)->select();
    }
    
    // 标记消息为已读
    public static function markAsRead($messageId)
    {
        return self::where('id', $messageId)->update(['is_read' => 1]);
    }
    
    // 获取未读消息数量
    public static function getUnreadCount($receiverCcal)
    {
        return self::where('receiver_ccal', $receiverCcal)->where('is_read', 0)->count();
    }
}
