<?php

return [
    // 应用名称
    'app_name' => env('app.name', 'CooChat'),
    // 应用调试模式
    'app_debug' => env('app.debug', true),
    // 应用密钥
    'app_key' => env('app.key', 'your-secret-key-here'),
    // 应用时区
    'app_timezone' => 'Asia/Shanghai',
    // 异常处理机制
    'exception_handle' => 'app\exception\Handle',
    // 自动加载的类库
    'autoload' => [
        'files' => [],
    ],
];
