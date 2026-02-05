<?php

// 应用入口文件

try {
    // 加载基础文件
    require __DIR__ . '/../vendor/autoload.php';
    
    // 执行应用
    $app = new app\App();
    $response = $app->http->run();
    $response->send();
    $app->http->end($response);
    
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
