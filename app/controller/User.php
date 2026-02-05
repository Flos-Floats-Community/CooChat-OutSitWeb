<?php

namespace app\controller;

use think\Controller;
use think\Request;
use app\model\User;
use app\model\Friend;
use app\utils\Auth;

class User extends Controller
{
    // 用户注册
    public function register(Request $request)
    {
        // 获取请求参数
        $username = $request->param('username');
        $password = $request->param('password');
        $now = $request->param('now', '');
        
        // 验证参数
        if (!$username || !$password) {
            return json([
                'code' => 400,
                'message' => '用户名和密码不能为空',
                'data' => null
            ]);
        }
        
        // 检查用户名是否已存在
        $existingUser = User::findByUsername($username);
        if ($existingUser) {
            return json([
                'code' => 400,
                'message' => '用户名已存在',
                'data' => null
            ]);
        }
        
        // 生成唯一的CCAL
        $ccal = User::generateCcal();
        
        // 密码哈希
        $passwordHash = Auth::passwordHash($password);
        
        // 创建用户
        $user = new User();
        $user->username = $username;
        $user->password = $passwordHash;
        $user->ccal = $ccal;
        $user->now = $now;
        
        if ($user->save()) {
            // 生成JWT令牌
            $token = Auth::generateToken($ccal);
            
            return json([
                'code' => 200,
                'message' => '注册成功',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'ccal' => $user->ccal,
                        'now' => $user->now,
                        'created_at' => $user->created_at
                    ],
                    'token' => $token
                ]
            ]);
        } else {
            return json([
                'code' => 500,
                'message' => '注册失败',
                'data' => null
            ]);
        }
    }
    
    // 用户登录
    public function login(Request $request)
    {
        // 获取请求参数
        $ccal = $request->param('ccal');
        $password = $request->param('password');
        
        // 验证参数
        if (!$ccal || !$password) {
            return json([
                'code' => 400,
                'message' => 'CCAL和密码不能为空',
                'data' => null
            ]);
        }
        
        // 查找用户
        $user = User::findByCcal($ccal);
        if (!$user) {
            return json([
                'code' => 400,
                'message' => '用户不存在',
                'data' => null
            ]);
        }
        
        // 验证密码
        if (!Auth::passwordVerify($password, $user->password)) {
            return json([
                'code' => 400,
                'message' => '密码错误',
                'data' => null
            ]);
        }
        
        // 生成JWT令牌
        $token = Auth::generateToken($ccal);
        
        return json([
            'code' => 200,
            'message' => '登录成功',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'ccal' => $user->ccal,
                    'now' => $user->now,
                    'created_at' => $user->created_at
                ],
                'token' => $token
            ]
        ]);
    }
    
    // 获取用户信息
    public function info(Request $request)
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
        $ccal = Auth::verifyToken($token);
        if (!$ccal) {
            return json([
                'code' => 401,
                'message' => '令牌无效',
                'data' => null
            ]);
        }
        
        // 查找用户
        $user = User::findByCcal($ccal);
        if (!$user) {
            return json([
                'code' => 400,
                'message' => '用户不存在',
                'data' => null
            ]);
        }
        
        return json([
            'code' => 200,
            'message' => '获取成功',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'ccal' => $user->ccal,
                'now' => $user->now,
                'created_at' => $user->created_at
            ]
        ]);
    }
    
    // 更新用户状态
    public function updateStatus(Request $request)
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
        $ccal = Auth::verifyToken($token);
        if (!$ccal) {
            return json([
                'code' => 401,
                'message' => '令牌无效',
                'data' => null
            ]);
        }
        
        // 获取请求参数
        $now = $request->param('now');
        
        // 查找用户
        $user = User::findByCcal($ccal);
        if (!$user) {
            return json([
                'code' => 400,
                'message' => '用户不存在',
                'data' => null
            ]);
        }
        
        // 更新状态
        $user->now = $now;
        if ($user->save()) {
            return json([
                'code' => 200,
                'message' => '更新成功',
                'data' => [
                    'now' => $user->now
                ]
            ]);
        } else {
            return json([
                'code' => 500,
                'message' => '更新失败',
                'data' => null
            ]);
        }
    }
    
    // 发送好友请求
    public function sendFriendRequest(Request $request)
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
        $userCcal = Auth::verifyToken($token);
        if (!$userCcal) {
            return json([
                'code' => 401,
                'message' => '令牌无效',
                'data' => null
            ]);
        }
        
        // 获取请求参数
        $friendCcal = $request->param('friend_ccal');
        
        // 验证参数
        if (!$friendCcal) {
            return json([
                'code' => 400,
                'message' => '好友CCAL不能为空',
                'data' => null
            ]);
        }
        
        // 检查好友是否存在
        $friendUser = User::findByCcal($friendCcal);
        if (!$friendUser) {
            return json([
                'code' => 400,
                'message' => '好友不存在',
                'data' => null
            ]);
        }
        
        // 发送好友请求
        $result = Friend::sendFriendRequest($userCcal, $friendCcal);
        
        if ($result) {
            return json([
                'code' => 200,
                'message' => '好友请求发送成功',
                'data' => null
            ]);
        } else {
            return json([
                'code' => 400,
                'message' => '好友请求发送失败，可能已经是好友或请求已存在',
                'data' => null
            ]);
        }
    }
    
    // 确认好友请求
    public function confirmFriendRequest(Request $request)
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
        $userCcal = Auth::verifyToken($token);
        if (!$userCcal) {
            return json([
                'code' => 401,
                'message' => '令牌无效',
                'data' => null
            ]);
        }
        
        // 获取请求参数
        $friendCcal = $request->param('friend_ccal');
        
        // 验证参数
        if (!$friendCcal) {
            return json([
                'code' => 400,
                'message' => '好友CCAL不能为空',
                'data' => null
            ]);
        }
        
        // 确认好友请求
        $result = Friend::confirmFriendRequest($userCcal, $friendCcal);
        
        if ($result) {
            return json([
                'code' => 200,
                'message' => '好友请求确认成功',
                'data' => null
            ]);
        } else {
            return json([
                'code' => 400,
                'message' => '好友请求确认失败，可能请求不存在',
                'data' => null
            ]);
        }
    }
    
    // 获取好友列表
    public function getFriendList(Request $request)
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
        $userCcal = Auth::verifyToken($token);
        if (!$userCcal) {
            return json([
                'code' => 401,
                'message' => '令牌无效',
                'data' => null
            ]);
        }
        
        // 获取好友列表
        $friends = Friend::getFriendList($userCcal);
        
        // 获取好友详细信息
        $friendDetails = [];
        foreach ($friends as $friend) {
            $friendUser = User::findByCcal($friend->friend_ccal);
            if ($friendUser) {
                $friendDetails[] = [
                    'id' => $friendUser->id,
                    'username' => $friendUser->username,
                    'ccal' => $friendUser->ccal,
                    'now' => $friendUser->now,
                    'created_at' => $friendUser->created_at
                ];
            }
        }
        
        return json([
            'code' => 200,
            'message' => '获取好友列表成功',
            'data' => [
                'friends' => $friendDetails
            ]
        ]);
    }
    
    // 删除好友
    public function deleteFriend(Request $request)
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
        $userCcal = Auth::verifyToken($token);
        if (!$userCcal) {
            return json([
                'code' => 401,
                'message' => '令牌无效',
                'data' => null
            ]);
        }
        
        // 获取请求参数
        $friendCcal = $request->param('friend_ccal');
        
        // 验证参数
        if (!$friendCcal) {
            return json([
                'code' => 400,
                'message' => '好友CCAL不能为空',
                'data' => null
            ]);
        }
        
        // 删除好友
        $result = Friend::deleteFriend($userCcal, $friendCcal);
        
        if ($result) {
            return json([
                'code' => 200,
                'message' => '删除好友成功',
                'data' => null
            ]);
        } else {
            return json([
                'code' => 400,
                'message' => '删除好友失败，可能不是好友',
                'data' => null
            ]);
        }
    }
}

