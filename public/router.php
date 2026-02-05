<?php

// 应用入口文件
namespace think;

try {
    // 加载基础文件
    require __DIR__ . '/../vendor/autoload.php';
    
    // 执行应用
    $app = new App();
    $app->run()->send();
    
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
