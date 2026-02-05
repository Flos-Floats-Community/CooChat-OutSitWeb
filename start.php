<?php

// 加载配置文件
require __DIR__ . '/vendor/autoload.php';

// 加载应用
$app = new app\App();

// 运行应用
$app->run()->send();
