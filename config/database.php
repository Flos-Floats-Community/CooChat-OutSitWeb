<?php

return [
    // 默认数据库连接
    'default' => env('database.driver', 'mysql'),
    
    // 数据库连接配置
    'connections' => [
        'mysql' => [
            'type' => 'mysql',
            'hostname' => env('database.hostname', 'localhost'),
            'database' => env('database.database', 'example_db'),
            'username' => env('database.username', 'admin'),
            'password' => env('database.password', 'password'),
            'hostport' => env('database.hostport', '3306'),
            'charset' => 'utf8mb4',
            'prefix' => 'cc_',
            'debug' => true,
        ],
    ],
];
