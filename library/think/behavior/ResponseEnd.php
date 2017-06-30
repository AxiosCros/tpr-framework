<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/6/30 16:40
 */
namespace think\behavior;

use think\Request;
use think\Loader;
use think\Config;

class ResponseEnd {
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
        $this->req        = $this->request->req;
        $this->mca        = $this->request->mca;
    }

    public function run(){
        $this->middleware();
    }

    private function middleware(){
        $middleware_config =  Config::get('middleware.after');
        if(isset($middleware_config[$this->mca])){
            $middleware_config = $middleware_config[$this->mca];
            $Middleware = validate($middleware_config[0]);
            if(isset($middleware_config[1]) && method_exists($Middleware,$middleware_config[1])){
                call_user_func_array([$Middleware,$middleware_config[1]],[$this->request]);
            }
        }else{
            $class = Loader::parseClass(strtolower($this->module), 'middleware',strtolower($this->controller),false);
            if(class_exists($class)){
                $Middleware = Loader::validate($this->controller, 'middleware', false,$this->module);
                if(method_exists($Middleware,'after')){
                    call_user_func_array([$Middleware,'after'],array($this->request,$this->req));
                }
            }
        }
    }
}