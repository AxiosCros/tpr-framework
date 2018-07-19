<?php

namespace tpr\framework;

// +----------------------------------------------------------------------
// | TPR-FRAMEWORK2.1 [ BASE ON ThinkPHP5.1 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: axioscros <axioscros@aliyun.com>
// +----------------------------------------------------------------------

// 载入Loader类
require_once __DIR__ . '/library/framework/Loader.php';

require_once __DIR__ . '/helper.php';

define("TPR_FRAMEWORK_NAMESPACE", "\\tpr\\framework\\");

// 注册自动加载
Loader::register();

// 实现日志接口
if (interface_exists('Psr\Log\LoggerInterface')) {
    interface LoggerInterface extends \Psr\Log\LoggerInterface
    {
    }
} else {
    interface LoggerInterface
    {
    }
}

$Container = new Container();

// 注册核心类到容器
$Container['app'] = new App();

Loader::addClassAlias([
    'App'     => \tpr\App::class,
    'Env'     => \tpr\Env::class,
    'Config'  => \tpr\Config::class,
    'Session' => \tpr\Session::class
]);

// 加载composer AutoFile文件
Loader::loadComposerAutoloadFiles();
