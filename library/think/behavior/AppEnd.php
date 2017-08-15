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
use think\Debug;
use think\Request;
use think\Response;

class AppEnd{

    public $request;

    public $req;

    function __construct()
    {
        $this->request    = Request::instance();
        $this->req        = $this->request->getContent();
    }

    public function run(){
        $this->cache();
    }

    private function cache(){
        Debug::save(ROOT_PATH . 'test.log' , ['req'=>$this->req , 'content'=>Response::create()->getData()]);
        CacheRequest::set($this->req,$this->request);
    }
}