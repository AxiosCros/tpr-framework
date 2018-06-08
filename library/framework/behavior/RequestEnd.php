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

namespace tpr\framework\behavior;

use tpr\db\Db;
use tpr\framework\Fork;

class RequestEnd extends Fork {

    public function run(){
        Db::clear();
        $queue = Fork::$queue;
        Fork::fork(true);
        Fork::doFork($queue);
        if(function_exists('posix_kill') && function_exists('posix_getpid')){
            posix_kill(posix_getpid(), SIGINT);
            exit();
        }
    }
}