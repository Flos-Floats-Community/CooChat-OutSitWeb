<?php

namespace app\utils;

use Firebase\JWT\JWT;
use think\facade\Config;

class Auth
{
    // 密码哈希
    public static function passwordHash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    
    // 密码验证
    public static function passwordVerify($password, $hash)
    {
        return password_verify($password, $hash);
    }
    
    // 生成JWT令牌
    public static function generateToken($ccal)
    {
        $key = Config::get('app.app_key');
        $payload = [
            'sub' => $ccal,
            'iat' => time(),
            'exp' => time() + 3600 * 24 // 24小时过期
        ];
        
        return JWT::encode($payload, $key, 'HS256');
    }
    
    // 验证JWT令牌
    public static function verifyToken($token)
    {
        try {
            $key = Config::get('app.app_key');
            $payload = JWT::decode($token, $key, ['HS256']);
            return $payload->sub;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    // 从请求头获取令牌
    public static function getTokenFromHeader()
    {
        $header = request()->header('Authorization');
        if ($header && strpos($header, 'Bearer ') === 0) {
            return substr($header, 7);
        }
        return false;
    }
}
