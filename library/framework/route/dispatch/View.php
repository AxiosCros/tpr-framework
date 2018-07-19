<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace tpr\framework\route\dispatch;

use tpr\framework\Response;
use tpr\framework\route\Dispatch;

class View extends Dispatch
{
    public function exec()
    {
        // 渲染模板输出
        $vars = array_merge($this->request->param(), $this->param);

        return Response::create($this->dispatch, 'view')->assign($vars);
    }
}
