<?php

namespace app\controller;

use think\Controller;
use think\Request;
use app\model\Message;
use app\utils\Auth;

class Chat extends Controller
{
    // 发送消息
    public function sendMessage(Request $request)
    {
        // 从请求头获取令牌
        $token = Auth::getTokenFromHeader();
        if (!$token) {
            return json([
                'code' => 401,
                'message' => '未授权',
                'data' => null
            ]);
        }
        
        // 验证令牌
        $senderCcal = Auth::verifyToken($token);
        if (!$senderCcal) {
            return json([
                'code' => 401,
                'message' => '令牌无效',
                'data' => null
            ]);
        }
        
        // 获取请求参数
        $receiverCcal = $request->param('receiver_ccal');
        $content = $request->param('content');
        $messageType = $request->param('message_type', 'text');
        
        // 验证参数
        if (!$receiverCcal || !$content) {
            return json([
                'code' => 400,
                'message' => '接收者CCAL和消息内容不能为空',
                'data' => null
            ]);
        }
        
        // 创建消息
        $message = new Message();
        $message->sender_ccal = $senderCcal;
        $message->receiver_ccal = $receiverCcal;
        $message->content = $content;
        $message->message_type = $messageType;
        $message->is_read = 0;
        
        if ($message->save()) {
            return json([
                'code' => 200,
                'message' => '发送成功',
                'data' => [
                    'message' => [
                        'id' => $message->id,
                        'sender_ccal' => $message->sender_ccal,
                        'receiver_ccal' => $message->receiver_ccal,
                        'content' => $message->content,
                        'message_type' => $message->message_type,
                        'is_read' => $message->is_read,
                        'created_at' => $message->created_at
                    ]
                ]
            ]);
        } else {
            return json([
                'code' => 500,
                'message' => '发送失败',
                'data' => null
            ]);
        }
    }
    
    // 获取聊天历史
    public function getHistory(Request $request)
    {
        // 从请求头获取令牌
        $token = Auth::getTokenFromHeader();
        if (!$token) {
            return json([
                'code' => 401,
                'message' => '未授权',
                'data' => null
            ]);
        }
        
        // 验证令牌
        $currentCcal = Auth::verifyToken($token);
        if (!$currentCcal) {
            return json([
                'code' => 401,
                'message' => '令牌无效',
                'data' => null
            ]);
        }
        
        // 获取请求参数
        $otherCcal = $request->param('other_ccal');
        $limit = $request->param('limit', 50);
        $offset = $request->param('offset', 0);
        
        // 验证参数
        if (!$otherCcal) {
            return json([
                'code' => 400,
                'message' => '对方CCAL不能为空',
                'data' => null
            ]);
        }
        
        // 获取聊天历史
        $messages = Message::getHistory($currentCcal, $otherCcal, $limit, $offset);
        
        return json([
            'code' => 200,
            'message' => '获取成功',
            'data' => [
                'messages' => $messages
            ]
        ]);
    }
    
    // 标记消息为已读
    public function markRead(Request $request)
    {
        // 从请求头获取令牌
        $token = Auth::getTokenFromHeader();
        if (!$token) {
            return json([
                'code' => 401,
                'message' => '未授权',
                'data' => null
            ]);
        }
        
        // 验证令牌
        $currentCcal = Auth::verifyToken($token);
        if (!$currentCcal) {
            return json([
                'code' => 401,
                'message' => '令牌无效',
                'data' => null
            ]);
        }
        
        // 获取请求参数
        $messageId = $request->param('message_id');
        
        // 验证参数
        if (!$messageId) {
            return json([
                'code' => 400,
                'message' => '消息ID不能为空',
                'data' => null
            ]);
        }
        
        // 标记消息为已读
        $result = Message::markAsRead($messageId);
        
        if ($result) {
            return json([
                'code' => 200,
                'message' => '标记成功',
                'data' => null
            ]);
        } else {
            return json([
                'code' => 500,
                'message' => '标记失败',
                'data' => null
            ]);
        }
    }
}
