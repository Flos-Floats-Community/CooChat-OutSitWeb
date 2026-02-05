<?php

use think\facade\Route;

// API路由组
Route::group('api', function() {
    // 用户相关路由
    Route::group('user', function() {
        Route::post('register', 'User/register');
        Route::post('login', 'User/login');
        Route::get('info', 'User/info');
        Route::put('status', 'User/updateStatus');
        // 好友相关路由
        Route::post('friend/request', 'User/sendFriendRequest');
        Route::post('friend/confirm', 'User/confirmFriendRequest');
        Route::get('friend/list', 'User/getFriendList');
        Route::delete('friend', 'User/deleteFriend');
    });
    
    // 聊天相关路由
    Route::group('chat', function() {
        Route::post('send', 'Chat/sendMessage');
        Route::get('history', 'Chat/getHistory');
        Route::put('read', 'Chat/markRead');
    });
});

// WebSocket路由
Route::rule('ws', 'WebSocket/connect', 'GET');
