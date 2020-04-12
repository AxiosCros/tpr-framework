<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//------------------------
// ThinkPHP 助手函数
//-------------------------

use tpr\db\Db;
use tpr\framework\Cache;
use tpr\framework\Config;
use tpr\framework\Cookie;
use tpr\framework\Debug;
use tpr\framework\exception\HttpException;
use tpr\framework\exception\HttpResponseException;
use tpr\framework\Lang;
use tpr\framework\Loader;
use tpr\framework\Log;
use tpr\framework\Request;
use tpr\framework\Response;
use tpr\framework\Session;
use tpr\framework\Url;

if (!function_exists('D')) {
    function D($table_name, $config = null)
    {
        return null === $config ? Db::name($table_name) : Db::connect($config)->name($table_name);
    }
}

if (!function_exists('u')) {
    function u($module = 'index', $controller = 'index', $action = 'index')
    {
        return url($module . '/' . $controller . '/' . $action);
    }
}

if (!function_exists('c')) {
    function c($index, $default = '')
    {
        $config = config($index);

        return null === $config ? $default : $config;
    }
}

if (!function_exists('env')) {
    function env($index, $default = '')
    {
        return \tpr\framework\Env::get($index, $default);
    }
}

if (!function_exists('load_trait')) {
    /**
     * 快速导入Traits PHP5.5以上无需调用.
     *
     * @param string $class trait库
     * @param string $ext   类库后缀
     *
     * @return bool
     */
    function load_trait($class, $ext = EXT)
    {
        return Loader::import($class, TRAIT_PATH, $ext);
    }
}

if (!function_exists('exception')) {
    /**
     * 抛出异常处理.
     *
     * @param string $msg       异常消息
     * @param int    $code      异常代码 默认为0
     * @param string $exception 异常类
     *
     * @throws Exception
     */
    function exception($msg, $code = 0, $exception = '')
    {
        $e = $exception ?: '\tpr\framework\Exception';

        throw new $e($msg, $code);
    }
}

if (!function_exists('debug')) {
    /**
     * 记录时间（微秒）和内存使用情况.
     *
     * @param string     $start 开始标签
     * @param string     $end   结束标签
     * @param int|string $dec   小数位 如果是m 表示统计内存占用
     *
     * @return mixed
     */
    function debug($start, $end = '', $dec = 6)
    {
        if ('' == $end) {
            Debug::remark($start);
        } else {
            return 'm' == $dec ? Debug::getRangeMem($start, $end) : Debug::getRangeTime($start, $end, $dec);
        }

        return true;
    }
}

if (!function_exists('lang')) {
    /**
     * 获取语言变量值
     *
     * @param string $name 语言变量名
     * @param array  $vars 动态变量值
     * @param string $lang 语言
     *
     * @return mixed
     */
    function lang($name, $vars = [], $lang = '')
    {
        return Lang::get($name, $vars, $lang);
    }
}

if (!function_exists('config')) {
    /**
     * 获取和设置配置参数.
     *
     * @param array|string $name  参数名
     * @param mixed        $value 参数值
     * @param string       $range 作用域
     *
     * @return mixed
     */
    function config($name = '', $value = null, $range = '')
    {
        if (null === $value && is_string($name)) {
            return 0 === strpos($name, '?') ? Config::has(substr($name, 1), $range) : Config::get($name, $range);
        }

        return Config::set($name, $value, $range);
    }
}

if (!function_exists('input')) {
    /**
     * 获取输入数据 支持默认值和过滤.
     *
     * @param string $key     获取的变量名
     * @param mixed  $default 默认值
     * @param string $filter  过滤方法
     *
     * @throws \tpr\framework\Exception
     *
     * @return mixed
     */
    function input($key = '', $default = null, $filter = '')
    {
        if (0 === strpos($key, '?')) {
            $key = substr($key, 1);
            $has = true;
        }
        if ($pos = strpos($key, '.')) {
            // 指定参数来源
            list($method, $key) = explode('.', $key, 2);
            if (!in_array($method, ['get', 'post', 'put', 'patch', 'delete', 'route', 'param', 'request', 'session', 'cookie', 'server', 'env', 'path', 'file'])) {
                $key    = $method . '.' . $key;
                $method = 'param';
            }
        } else {
            // 默认为自动判断
            $method = 'param';
        }
        if (isset($has)) {
            return request()->has($key, $method, $default);
        }

        return request()->{$method}($key, $default, $filter);
    }
}

if (!function_exists('widget')) {
    /**
     * 渲染输出Widget.
     *
     * @param string $name Widget名称
     * @param array  $data 传入的参数
     *
     * @throws ReflectionException
     * @throws \tpr\framework\Exception
     *
     * @return bool|mixed
     */
    function widget($name, $data = [])
    {
        return Loader::action($name, $data, 'widget');
    }
}

if (!function_exists('model')) {
    /**
     * 实例化Model.
     *
     * @param string $name         Model名称
     * @param string $layer        业务层名称
     * @param bool   $appendSuffix 是否添加类名后缀
     *
     * @return object
     */
    function model($name = '', $layer = 'model', $appendSuffix = false)
    {
        return Loader::model($name, $layer, $appendSuffix);
    }
}

if (!function_exists('validate')) {
    /**
     * 实例化验证器.
     *
     * @param string $name         验证器名称
     * @param string $layer        业务层名称
     * @param bool   $appendSuffix 是否添加类名后缀
     *
     * @return false|object
     */
    function validate($name = '', $layer = 'validate', $appendSuffix = false)
    {
        return Loader::validate($name, $layer, $appendSuffix);
    }
}

if (!function_exists('controller')) {
    /**
     * 实例化控制器 格式：[模块/]控制器.
     *
     * @param string $name         资源地址
     * @param string $layer        控制层名称
     * @param bool   $appendSuffix 是否添加类名后缀
     *
     * @throws ReflectionException
     * @throws \tpr\framework\Exception
     *
     * @return null|mixed
     */
    function controller($name, $layer = 'controller', $appendSuffix = false)
    {
        return Loader::controller($name, $layer, $appendSuffix);
    }
}

if (!function_exists('action')) {
    /**
     * 调用模块的操作方法 参数格式 [模块/控制器/]操作.
     *
     * @param string       $url          调用地址
     * @param array|string $vars         调用参数 支持字符串和数组
     * @param string       $layer        要调用的控制层名称
     * @param bool         $appendSuffix 是否添加类名后缀
     *
     * @throws ReflectionException
     * @throws \tpr\framework\Exception
     *
     * @return bool|mixed
     */
    function action($url, $vars = [], $layer = 'controller', $appendSuffix = false)
    {
        return Loader::action($url, $vars, $layer, $appendSuffix);
    }
}

if (!function_exists('import')) {
    /**
     * 导入所需的类库 同java的Import 本函数有缓存功能.
     *
     * @param string $class   类库命名空间字符串
     * @param string $baseUrl 起始路径
     * @param string $ext     导入的文件扩展名
     *
     * @return bool
     */
    function import($class, $baseUrl = '', $ext = EXT)
    {
        return Loader::import($class, $baseUrl, $ext);
    }
}

if (!function_exists('vendor')) {
    /**
     * 快速导入第三方框架类库 所有第三方框架的类库文件统一放到 系统的Vendor目录下面.
     *
     * @param string $class 类库
     * @param string $ext   类库后缀
     *
     * @return bool
     */
    function vendor($class, $ext = EXT)
    {
        return Loader::import($class, VENDOR_PATH, $ext);
    }
}

if (!function_exists('dump')) {
    /**
     * 浏览器友好的变量输出.
     *
     * @param mixed  $var   变量
     * @param bool   $echo  是否输出 默认为true 如果为false 则返回输出字符串
     * @param string $label 标签 默认为空
     *
     * @return string
     */
    function dump($var, $echo = true, $label = null)
    {
        return Debug::dump($var, $echo, $label);
    }
}

if (!function_exists('url')) {
    /**
     * Url生成.
     *
     * @param string       $url    路由地址
     * @param array|string $vars   变量
     * @param bool|string  $suffix 生成的URL后缀
     * @param bool|string  $domain 域名
     *
     * @return string
     */
    function url($url = '', $vars = '', $suffix = true, $domain = false)
    {
        return Url::build($url, $vars, $suffix, $domain);
    }
}

if (!function_exists('session')) {
    /**
     * Session管理.
     *
     * @param array|string $name   session名称，如果为数组表示进行session设置
     * @param mixed        $value  session值
     * @param string       $prefix 前缀
     *
     * @throws \tpr\framework\exception\PermissionDenied
     *
     * @return null|array|bool
     */
    function session($name, $value = '', $prefix = null)
    {
        if (is_array($name)) {
            // 初始化
            Session::init($name);
        } elseif (null === $name) {
            // 清除
            Session::clear('' === $value ? null : $value);
        } elseif ('' === $value) {
            // 判断或获取
            return 0 === strpos($name, '?') ? Session::has(substr($name, 1), $prefix) : Session::get($name, $prefix);
        } elseif (null === $value) {
            // 删除
            Session::delete($name, $prefix);
        } else {
            // 设置
            Session::set($name, $value, $prefix);
        }

        return true;
    }
}

if (!function_exists('cookie')) {
    /**
     * Cookie管理.
     *
     * @param array|string $name   cookie名称，如果为数组表示进行cookie设置
     * @param mixed        $value  cookie值
     * @param mixed        $option 参数
     *
     * @return mixed
     */
    function cookie($name, $value = '', $option = null)
    {
        if (is_array($name)) {
            // 初始化
            Cookie::init($name);
        } elseif (null === $name) {
            // 清除
            Cookie::clear($value);
        } elseif ('' === $value) {
            // 获取
            return 0 === strpos($name, '?') ? Cookie::has(substr($name, 1), $option) : Cookie::get($name, $option);
        } elseif (null === $value) {
            // 删除
            Cookie::delete($name);
        } else {
            // 设置
            Cookie::set($name, $value, $option);
        }

        return true;
    }
}

if (!function_exists('cache')) {
    /**
     * 缓存管理.
     *
     * @param mixed  $name    缓存名称，如果为数组表示进行缓存设置
     * @param mixed  $value   缓存值
     * @param mixed  $options 缓存参数
     * @param string $tag     缓存标签
     *
     * @return mixed
     */
    function cache($name, $value = '', $options = null, $tag = null)
    {
        if (is_array($options)) {
            // 缓存操作的同时初始化
            $cache = Cache::connect($options);
        } elseif (is_array($name)) {
            // 缓存初始化
            return Cache::connect($name);
        } else {
            $cache = Cache::init();
        }

        if (null === $name) {
            return $cache->clear($value);
        }
        if ('' === $value) {
            // 获取缓存
            return 0 === strpos($name, '?') ? $cache->has(substr($name, 1)) : $cache->get($name);
        }
        if (null === $value) {
            // 删除缓存
            return $cache->rm($name);
        }
        if (0 === strpos($name, '?') && '' !== $value) {
            $expire = is_numeric($options) ? $options : null;

            return $cache->remember(substr($name, 1), $value, $expire);
        }
        // 缓存数据
        if (is_array($options)) {
            $expire = isset($options['expire']) ? $options['expire'] : null; //修复查询缓存无法设置过期时间
        } else {
            $expire = is_numeric($options) ? $options : null; //默认快捷缓存设置过期时间
        }
        if (null === $tag) {
            return $cache->set($name, $value, $expire);
        }

        return $cache->tag($tag)->set($name, $value, $expire);
    }
}

if (!function_exists('trace')) {
    /**
     * 记录日志信息.
     *
     * @param mixed  $log   log信息 支持字符串和数组
     * @param string $level 日志级别
     *
     * @return array
     */
    function trace($log = '[think]', $level = 'log')
    {
        if ('[think]' === $log) {
            return Log::getLog();
        }
        Log::record($log, $level);

        return [];
    }
}

if (!function_exists('request')) {
    /**
     * 获取当前Request对象实例.
     *
     * @return Request
     */
    function request()
    {
        return Request::instance();
    }
}

if (!function_exists('response')) {
    /**
     * 创建普通 Response 对象实例.
     *
     * @param mixed      $data   输出数据
     * @param int|string $code   状态码
     * @param array      $header 头信息
     * @param string     $type
     *
     * @return Response
     */
    function response($data = [], $code = 200, $header = [], $type = 'html')
    {
        return Response::create($data, $type, $code, $header);
    }
}

if (!function_exists('view')) {
    /**
     * 渲染模板输出.
     *
     * @param string $template 模板文件
     * @param array  $vars     模板变量
     * @param array  $replace  模板替换
     * @param int    $code     状态码
     *
     * @return \tpr\framework\response\View
     */
    function view($template = '', $vars = [], $replace = [], $code = 200)
    {
        return Response::create($template, 'view', $code)->replace($replace)->assign($vars);
    }
}

if (!function_exists('json')) {
    /**
     * 获取\think\response\Json对象实例.
     *
     * @param mixed $data    返回的数据
     * @param int   $code    状态码
     * @param array $header  头部
     * @param array $options 参数
     *
     * @return \tpr\framework\response\Json
     */
    function json($data = [], $code = 200, $header = [], $options = [])
    {
        return Response::create($data, 'json', $code, $header, $options);
    }
}

if (!function_exists('jsonp')) {
    /**
     * 获取\think\response\Jsonp对象实例.
     *
     * @param mixed $data    返回的数据
     * @param int   $code    状态码
     * @param array $header  头部
     * @param array $options 参数
     *
     * @return \tpr\framework\response\Jsonp
     */
    function jsonp($data = [], $code = 200, $header = [], $options = [])
    {
        return Response::create($data, 'jsonp', $code, $header, $options);
    }
}

if (!function_exists('xml')) {
    /**
     * 获取\think\response\Xml对象实例.
     *
     * @param mixed $data    返回的数据
     * @param int   $code    状态码
     * @param array $header  头部
     * @param array $options 参数
     *
     * @return \tpr\framework\response\Xml
     */
    function xml($data = [], $code = 200, $header = [], $options = [])
    {
        return Response::create($data, 'xml', $code, $header, $options);
    }
}

if (!function_exists('redirect')) {
    /**
     * 获取\think\response\Redirect对象实例.
     *
     * @param mixed     $url    重定向地址 支持Url::build方法的地址
     * @param array|int $params 额外参数
     * @param int       $code   状态码
     * @param array     $with   隐式传参
     *
     * @throws \tpr\framework\exception\PermissionDenied
     *
     * @return Response|\tpr\framework\response\Json|\tpr\framework\response\Jsonp|\tpr\framework\response\Redirect|\tpr\framework\response\View|\tpr\framework\response\Xml
     */
    function redirect($url = [], $params = [], $code = 302, $with = [])
    {
        if (is_int($params)) {
            $code   = $params;
            $params = [];
        }

        return Response::create($url, 'redirect', $code)->params($params)->with($with);
    }
}

if (!function_exists('abort')) {
    /**
     * 抛出HTTP异常.
     *
     * @param int|Response $code    状态码 或者 Response对象实例
     * @param string       $message 错误信息
     * @param array        $header  参数
     */
    function abort($code, $message = null, $header = [])
    {
        if ($code instanceof Response) {
            throw new HttpResponseException($code);
        }

        throw new HttpException($code, $message, null, $header);
    }
}

if (!function_exists('halt')) {
    /**
     * 调试变量并且中断输出.
     *
     * @param mixed $var 调试变量或者信息
     */
    function halt($var)
    {
        dump($var);

        throw new HttpResponseException(new Response());
    }
}

if (!function_exists('token')) {
    /**
     * 生成表单令牌.
     *
     * @param string $name 令牌名称
     * @param mixed  $type 令牌生成方法
     *
     * @throws \tpr\framework\Exception
     *
     * @return string
     */
    function token($name = '__token__', $type = 'md5')
    {
        $token = Request::instance()->token($name, $type);

        return '<input type="hidden" name="' . $name . '" value="' . $token . '" />';
    }
}
