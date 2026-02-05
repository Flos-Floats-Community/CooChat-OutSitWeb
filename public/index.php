<?php

use think\App;
use think\facade\Env;

// 加载基础文件
require __DIR__ . '/../vendor/autoload.php';

// 执行应用
$app = new App();
$app->run()->send();