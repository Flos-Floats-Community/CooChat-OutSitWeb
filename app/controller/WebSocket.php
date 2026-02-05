<?php

namespace app\controller;

use think\Controller;
use app\utils\Auth;
use Workerman\Worker;
use Workerman\Connection\TcpConnection;

class WebSocket extends Controller
{
    // WebSocket连接
    public function connect()
    {
        // 从查询参数获取令牌
        $token = request()->param('token');
        if (!$token) {
            return json([
                'code' => 401,
                'message' => '未授权',
                'data' => null
            ]);
        }
        
        // 验证令牌
        $ccal = Auth::verifyToken($token);
        if (!$ccal) {
            return json([
                'code' => 401,
                'message' => '令牌无效',
                'data' => null
            ]);
        }
        
        // 启动WebSocket服务
        $wsWorker = new Worker('websocket://0.0.0.0:8282');
        
        // 连接映射，用于存储CCAL和连接的对应关系
        $connections = [];
        
        // 当有连接建立时
        $wsWorker->onConnect = function(TcpConnection $connection) use (&$connections, $ccal) {
            // 存储连接
            $connections[$ccal] = $connection;
            $connection->ccal = $ccal;
            
            // 发送连接成功消息
            $connection->send(json_encode([
                'type' => 'connect',
                'message' => '连接成功',
                'ccal' => $ccal
            ]));
        };
        
        // 当收到消息时
        $wsWorker->onMessage = function(TcpConnection $connection, $data) use (&$connections) {
            $message = json_decode($data, true);
            
            // 处理消息
            if (isset($message['type']) && $message['type'] == 'chat') {
                $receiverCcal = $message['receiver_ccal'];
                $content = $message['content'];
                
                // 检查接收者是否在线
                if (isset($connections[$receiverCcal])) {
                    // 发送消息给接收者
                    $connections[$receiverCcal]->send(json_encode([
                        'type' => 'chat',
                        'sender_ccal' => $connection->ccal,
                        'content' => $content,
                        'timestamp' => time()
                    ]));
                    
                    // 发送确认消息给发送者
                    $connection->send(json_encode([
                        'type' => 'ack',
                        'message' => '消息已发送',
                        'receiver_ccal' => $receiverCcal
                    ]));
                } else {
                    // 发送离线消息给发送者
                    $connection->send(json_encode([
                        'type' => 'error',
                        'message' => '接收者离线',
                        'receiver_ccal' => $receiverCcal
                    ]));
                }
            }
        };
        
        // 当连接关闭时
        $wsWorker->onClose = function(TcpConnection $connection) use (&$connections) {
            // 从连接映射中移除
            if (isset($connection->ccal)) {
                unset($connections[$connection->ccal]);
            }
        };
        
        // 启动服务
        Worker::runAll();
    }
}
