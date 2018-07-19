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

class Redirect extends Dispatch
{
    public function exec()
    {
        return Response::create($this->dispatch, 'redirect')->code($this->code);
    }
}
