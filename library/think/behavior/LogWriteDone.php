<?php

// +----------------------------------------------------------------------
// | TPR [ Design For Api Develop ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2017 http://hanxv.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: axios <axioscros@aliyun.com>
// +----------------------------------------------------------------------

namespace think\behavior;

use think\Fork;
use think\Request;
use think\Tool;

/**
 * Class LogWriteDone.
 */
class LogWriteDone extends Fork
{
    public $param;
    public $request;

    public function __construct()
    {
        $this->request = Request::instance();
        $this->param = $this->request->param();
    }

    public function run()
    {
        $identity = Tool::identity();
        if ($identity == 2 && function_exists('posix_kill') && function_exists('posix_getpid')) {
            posix_kill(posix_getpid(), SIGINT);
            exit();
        }
    }
}
