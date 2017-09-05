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

use think\cache\CacheRequest;
use think\Db;
use think\Fork;
use think\Request;
use think\Response;

class RequestEnd extends Fork {

    public $result = [];

    public $request;

    function __construct()
    {
        $this->request = Request::instance();
        if(!empty(Request::instance())){
            $this->result  = Response::instance()->getData();
        }
    }

    public function run(){
        $this->cache();
        Db::clear();
        $queue = Fork::$queue;
        Fork::fork(true);
        Fork::doFork($queue);
        if(function_exists('posix_kill') && function_exists('posix_getpid')){
            posix_kill(posix_getpid(), SIGINT);
            exit();
        }
    }

    private function cache(){
        CacheRequest::set($this->result,$this->request);
    }
}