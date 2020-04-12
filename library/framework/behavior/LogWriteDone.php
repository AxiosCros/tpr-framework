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

use tpr\framework\Request;

/**
 * Class LogWriteDone.
 */
class LogWriteDone
{
    public $param;
    public $request;

    public function __construct()
    {
        $this->request = Request::instance();
        $this->param   = $this->request->param();
    }

    public function run()
    {
    }
}
