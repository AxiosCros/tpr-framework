<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018/6/28 16:33
 */

namespace tpr\framework;

use tpr\Env;
use tpr\Config;
use tpr\framework\exception\ClassNotFoundException;
use tpr\framework\exception\Handle;
use tpr\framework\exception\HttpResponseException;
use tpr\framework\route\Dispatch;
use tpr\Lang;
use tpr\Route;
use tpr\Request;
use tpr\Build;
use tpr\Hook;

class App extends Container
{
    protected $namespace;

    protected $container;

    protected $script_path;

    protected $root_path;

    protected $app_path;

    protected $framework_path;

    protected $config_path;

    protected $route_path;

    protected $runtime_path;

    protected $library_path;

    protected $vendor_path;

    protected $config_ext;

    protected $app_debug;

    protected $request;

    protected $app_config;

    protected $dispatch;

    protected $route_must;

    /**
     * 应用类库后缀
     * @var bool
     */
    protected $suffix = false;

    public function __construct()
    {
        $this->container  = Container::getInstance();
        $this->app_config = Config::pull('app');
        $this->request    = Request::init($this->app_config);
    }

    protected function init($options = [])
    {
        $this->script_path    = $options['script_path'] ?? dirname($_SERVER['SCRIPT_FILENAME']) . '/';
        $this->root_path      = $options['root_path'] ?? dirname($this->script_path) . '/';
        $this->app_path       = $options['app_path'] ?? $this->root_path . 'application/';
        $this->framework_path = $options['framework_path'] ?? $this->root_path . 'vendor/axios/tpr-framework/';
        $this->config_path    = $options['config_path'] ?? $this->root_path . 'config/';
        $this->route_path     = $options['route_path'] ?? $this->config_path . 'route/';
        $this->runtime_path   = $options['runtime_path'] ?? $this->root_path . 'runtime/';
        $this->library_path   = $options['library_path'] ?? $this->root_path . 'library/';
        $this->vendor_path    = $options['vendor_path'] ?? $this->root_path . 'vendor/';
        $this->namespace      = $options['namespace'] ?? 'app';
        $this->config_ext     = $options['config_ext'] ?? '.php';

        // 设置路径环境变量
        Env::set([
            'framework_path' => $this->framework_path,
            'root_path'      => $this->root_path,
            'app_path'       => $this->app_path,
            'config_path'    => $this->config_path,
            'route_path'     => $this->route_path,
            'runtime_path'   => $this->runtime_path,
            'library_path'   => $this->library_path,
            'vendor_path'    => $this->vendor_path,
            'app_namespace'  => $this->namespace,
            'config_ext'     => $this->config_ext
        ]);

        // 加载环境变量配置文件
        // 加载环境变量配置文件
        if (is_file($this->root_path . '.env')) {
            Env::load($this->root_path . '.env');
        }

        // 注册应用命名空间
        Loader::addNamespace($this->namespace, $this->app_path);

        // 应用初始化载入
        $this->loader();

        //调试模式
        $this->app_debug = Env::get('app_debug', $this->config('app.app_debug'));
        Env::set('app_debug', $this->app_debug);

        if (!$this->app_debug) {
            ini_set('display_errors', 'Off');
        } elseif (PHP_SAPI != 'cli') {
            //重新申请一块比较大的buffer
            if (ob_get_level() > 0) {
                $output = ob_get_clean();
            }
            ob_start();
            if (!empty($output)) {
                echo $output;
            }
        }

        // 注册异常处理类
        if ($this->config('app.exception_handle')) {
            Error::setExceptionHandler($this->config('app.exception_handle'));
        }

        // 加载composer autofile文件
        Loader::loadComposerAutoloadFiles();

        // 注册类库别名
        Loader::addClassAlias(Config::pull('alias'));

        // 设置系统时区
        date_default_timezone_set($this->config('app.default_timezone'));

        // 读取语言包
        $this->loadLangPack();

        // 路由初始化
        $this->routeInit();
    }

    /**
     * @param array $options
     * @return Response
     * @throws \Exception
     */
    public function run($options = [])
    {
        try{
            $this->init($options);
            Hook::listen('app_init');

            $dispatch = $this->dispatch;

            if (empty($dispatch)) {
                // 路由检测
                $dispatch = $this->routeCheck()->init();
            }

        }catch (HttpResponseException $exception){
            $dispatch = null;
            return $exception->getResponse();
        }catch (Exception $exception){
            $handle = new Handle();
            return $handle->render($exception);
        }

        return null;
    }

    protected function loader()
    {
        if (!file_exists($this->config_path)) {
            mkdir($this->config_path, 0777, true);
        }
        // 加载全局配置文件
        $this->loaderConfig($this->config_path);

        // 加载应用配置文件
        $this->loaderConfig($this->app_path);
    }

    private function loaderConfig($path)
    {
        $config_files = scandir($path);
        foreach ($config_files as $file) {
            if ('.' . pathinfo($file, PATHINFO_EXTENSION) === $this->config_ext) {
                $filename = $this->config_path . DIRECTORY_SEPARATOR . $file;
                Config::load($filename, pathinfo($file, PATHINFO_FILENAME));
            }
        }
    }

    public function getRootPath(){
        return $this->root_path;
    }

    public function getFrameworkPath(){
        return $this->framework_path;
    }

    public function getConfigPath(){
        return $this->config_path;
    }

    /**
     * 获取应用类库目录
     * @access public
     * @return string
     */
    public function getAppPath()
    {
        if (is_null($this->app_path)) {
            $this->app_path = self::getRootPath() . 'application' . DIRECTORY_SEPARATOR;
        }

        return $this->app_path;
    }

    public function getRuntimePath(){
        return $this->runtime_path;
    }

    /**
     * 获取应用类库命名空间
     * @access public
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    protected function loadLangPack()
    {
        // 读取默认语言
        Lang::range($this->config('app.default_lang'));

        if ($this->config('app.lang_switch_on')) {
            // 开启多语言机制 检测当前语言
            Lang::detect();
        }

        $this->request->setLangset(Lang::range());

        // 加载系统语言包
        Lang::load([
            $this->framework_path . 'lang' . DIRECTORY_SEPARATOR . $this->request->langset() . '.php',
            $this->app_path . 'lang' . DIRECTORY_SEPARATOR . $this->request->langset() . '.php',
        ]);
    }

    /**
     * 路由初始化 导入路由定义规则
     * @access public
     * @return void
     */
    public function routeInit()
    {
        // 路由检测
        $files = scandir($this->route_path);
        foreach ($files as $file) {
            if (strpos($file, '.php')) {
                $filename = $this->route_path . $file;
                // 导入路由配置
                $rules = include $filename;
                if (is_array($rules)) {
                    Route::import($rules);
                }
            }
        }

        if (Route::config('route_annotation')) {
            // 自动生成路由定义
            if ($this->app_debug) {
                Build::buildRoute(Route::config('controller_suffix'));
            }

            $filename = $this->runtime_path . 'build_route.php';

            if (is_file($filename)) {
                include $filename;
            }
        }
    }

    /**
     * 实例化验证类 格式：[模块名/]验证器名
     * @access public
     * @param  string $name         资源地址
     * @param  string $layer        验证层名称
     * @param  bool   $appendSuffix 是否添加类名后缀
     * @param  string $common       公共模块名
     * @return Validate|object
     * @throws ClassNotFoundException
     */
    public function validate($name = '', $layer = 'validate', $appendSuffix = false, $common = 'common')
    {
        $name = $name ?: $this->config('default_validate');

        if (empty($name)) {
            return new Validate;
        }

        return $this->create($name, $layer, $appendSuffix, $common);
    }

    /**
     * 实例化应用类库
     * @access public
     * @param  string $name         类名称
     * @param  string $layer        业务层名称
     * @param  bool   $appendSuffix 是否添加类名后缀
     * @param  string $common       公共模块名
     * @return object
     * @throws ClassNotFoundException
     */
    public function create($name, $layer, $appendSuffix = false, $common = 'common')
    {
        $guid = $name . $layer;

        if ($this->__isset($guid)) {
            return $this->__get($guid);
        }

        list($module, $class) = $this->parseModuleAndClass($name, $layer, $appendSuffix);

        if (class_exists($class)) {
            $object = $this->__get($class);
        } else {
            $class = str_replace('\\' . $module . '\\', '\\' . $common . '\\', $class);
            if (class_exists($class)) {
                $object = $this->__get($class);
            } else {
                throw new ClassNotFoundException('class not exists:' . $class, $class);
            }
        }

        $this->__set($guid, $class);

        return $object;
    }

    /**
     * 解析模块和类名
     * @access protected
     * @param  string $name         资源地址
     * @param  string $layer        验证层名称
     * @param  bool   $appendSuffix 是否添加类名后缀
     * @return array
     */
    protected function parseModuleAndClass($name, $layer, $appendSuffix)
    {
        if (false !== strpos($name, '\\')) {
            $class  = $name;
            $module = $this->request->module();
        } else {
            if (strpos($name, '/')) {
                list($module, $name) = explode('/', $name, 2);
            } else {
                $module = $this->request->module();
            }

            $class = $this->parseClass($module, $layer, $name, $appendSuffix);
        }

        return [$module, $class];
    }

    /**
     * URL路由检测（根据PATH_INFO)
     * @access public
     * @return Dispatch
     */
    public function routeCheck()
    {
        // 检测路由缓存
        if (!$this->app_debug && Config::get('route_check_cache')) {
            $routeKey = $this->getRouteCacheKey();
            $option   = Config::get('route_cache_option') ?: $this->cache->getConfig();

            if ($this->cache->connect($option)->has($routeKey)) {
                return $this->cache->connect($option)->get($routeKey);
            }
        }

        // 获取应用调度信息
        $path = $this->request->path();

        // 是否强制路由模式
        $must = !is_null($this->route_must) ? $this->route_must : Route::config('url_route_must');

        // 路由检测 返回一个Dispatch对象
        $dispatch = Route::check($path, $must);

        if (!empty($routeKey)) {
            try {
                $this->cache->connect($option)
                    ->tag('route_cache')
                    ->set($routeKey, $dispatch);
            } catch (\Exception $e) {
                // 存在闭包的时候缓存无效
            }
        }

        return $dispatch;
    }

    /**
     * 解析应用类的类名
     * @access public
     * @param  string $module 模块名
     * @param  string $layer  层名 controller model ...
     * @param  string $name   类名
     * @param  bool   $appendSuffix
     * @return string
     */
    public function parseClass($module, $layer, $name, $appendSuffix = false)
    {
        $name  = str_replace(['/', '.'], '\\', $name);
        $array = explode('\\', $name);
        $class = Loader::parseName(array_pop($array), 1) . ($this->suffix || $appendSuffix ? ucfirst($layer) : '');
        $path  = $array ? implode('\\', $array) . '\\' : '';

        return $this->namespace . '\\' . ($module ? $module . '\\' : '') . $layer . '\\' . $path . $class;
    }

    /**
     * 绑定一个类、闭包、实例、接口实现到容器
     * @access public
     * @param  string|array  $abstract    类标识、接口
     * @param  mixed         $concrete    要绑定的类、闭包或者实例
     * @return $this
     */
    public function bindTo($abstract, $concrete = null)
    {
        if (is_array($abstract)) {
            $this->bind = array_merge($this->bind, $abstract);
        } elseif ($concrete instanceof \Closure) {
            $this->bind[$abstract] = $concrete;
        } elseif (is_object($concrete)) {
            $this->instances[$abstract] = $concrete;
        } else {
            $this->bind[$abstract] = $concrete;
        }

        return $this;
    }

    /**
     * 是否为调试模式
     * @access public
     * @return bool
     */
    public function isDebug()
    {
        return $this->app_debug;
    }

    /**
     * 获取配置参数 为空则获取所有配置
     * @access public
     * @param  string $name 配置参数名（支持二级配置 .号分割）
     * @return mixed
     */
    public function config($name = '')
    {
        return Config::get($name);
    }

    public function __set($name, $value)
    {
        $this->bindTo($name, $value);
    }

    public function __get($name)
    {
        return $this->make($name);
    }

    public function __isset($name)
    {
        return $this->bound($name);
    }

    public function __unset($name)
    {
        $this->delete($name);
    }

    public function offsetExists($key)
    {
        return $this->__isset($key);
    }

    public function offsetGet($key)
    {
        return $this->__get($key);
    }

    public function offsetSet($key, $value)
    {
        $this->__set($key, $value);
    }

    public function offsetUnset($key)
    {
        $this->__unset($key);
    }
}