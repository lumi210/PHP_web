<?php

use app\AppService;
use bd\service\cmsService;
use app\common\service\moduleService;

// 系统服务定义文件
// 服务在完成全局初始化之后执行
return [
    AppService::class,
    moduleService::class,
    cmsService::class
];
