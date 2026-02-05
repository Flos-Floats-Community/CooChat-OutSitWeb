<?php

namespace app\exception;

use think\exception\Handle as BaseHandle;
use think\response\Json;
use Throwable;

class Handle extends BaseHandle
{
    public function render($request, Throwable $e): Json
    {
        // 返回JSON格式的错误信息
        return json([
            'code' => $e->getCode() ?: 500,
            'message' => $e->getMessage(),
            'data' => null
        ], $e->getCode() ?: 500);
    }
}
