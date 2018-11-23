<?php
/**
 * @author  : Axios
 * @email   : axioscros@aliyun.com
 * @blog    :  http://hanxv.cn
 * @datetime: 2017/6/30 16:40
 */

namespace tpr\framework\behavior;

use tpr\framework\exception\ClassNotFoundException;
use tpr\framework\Request;
use tpr\framework\Config;

class ResponseEnd
{
    public $param;
    public $request;
    public $module;
    public $controller;
    public $action;
    public $req;
    public $mca;

    function __construct()
    {
        $this->request    = Request::instance();
        $this->param      = $this->request->param();
        $this->module     = strtolower($this->request->module());
        $this->controller = strtolower($this->request->controller());
        $this->action     = $this->request->action();
        $this->req        = $this->request->getContent();
        $this->mca        = $this->module . '/' . $this->controller . '/' . $this->action;
    }

    public function run()
    {
        $this->middleware();
    }

    private function middleware()
    {
        $middleware_config = Config::get('middleware.after');
        if (!empty($middleware_config)) {
            if (isset($middleware_config[$this->mca])) {
                $middleware_config = $middleware_config[$this->mca];
                try {
                    $Middleware = validate($middleware_config[0], 'middleware');
                } catch (ClassNotFoundException $e) {
                    throw new ClassNotFoundException('class not exists:' . $middleware_config[0], __CLASS__);
                }

                if (isset($middleware_config[1]) && method_exists($Middleware, $middleware_config[1])) {
                    call_user_func_array([$Middleware, $middleware_config[1]], [$this->request]);
                }
            }
        }
    }
}