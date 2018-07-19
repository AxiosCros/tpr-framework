<?php
/**
 * @author: axios
 * @email: axiosleo@foxmail.com
 * @blog:  http://hanxv.cn
 * @datetime: 2018/6/28 16:52
 */

namespace tpr\framework;


class Facade
{
    protected static $instance;

    /**
     * 绑定对象
     * @var array
     */
    protected static $bind = [
        \tpr\App::class      => App::class,
        \tpr\Build::class    => Build::class,
        \tpr\Config::class   => Config::class,
        \tpr\Cookie::class   => Cookie::class,
        \tpr\Env::class      => Env::class,
        \tpr\Error::class    => Error::class,
        \tpr\Facade::class   => Facade::class,
        \tpr\File::class     => File::class,
        \tpr\Lang::class     => Lang::class,
        \tpr\Request::class  => Request::class,
        \tpr\Response::class => Response::class,
        \tpr\Route::class    => Route::class,
        \tpr\Session::class  => Session::class,
        \tpr\Validate::class => Validate::class,
    ];

    /**
     * 始终创建新的对象实例
     * @var bool
     */
    protected static $alwaysNewInstance;

    /**
     * 绑定类的静态代理
     * @static
     * @access public
     * @param  string|array $name 类标识
     * @param  string $class 类名
     * @return object|string
     */
    protected static function bind($name, $class = null)
    {
        if (__CLASS__ != static::class) {
            return self::__callStatic('bind', func_get_args());
        }

        if (is_array($name)) {
            self::$bind = array_merge(self::$bind, $name);
        } else {
            self::$bind[$name] = $class;
        }
        return $class;
    }

    /**
     * 创建Facade实例
     * @static
     * @access protected
     * @param  string $class 类名或标识
     * @param  array $args 变量
     * @param  bool $newInstance 是否每次创建新的实例
     * @return object
     * @throws \Exception
     */
    protected static function createFacade($class = '', $args = [], $newInstance = false)
    {
        $class       = $class ?: static::class;
        $facadeClass = static::getFacadeClass();

        if ($facadeClass) {
            $class = $facadeClass;
        } elseif (isset(self::$bind[$class])) {
            $class = self::$bind[$class];
        }

        if (static::$alwaysNewInstance) {
            $newInstance = true;
        }

        return Container::getInstance()->make($class, $args, $newInstance);
    }

    /**
     * 获取当前Facade对应类名
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return static::$instance;
    }

    /**
     * 带参数实例化当前Facade类
     * @access public
     * @param mixed ...$args
     * @return object
     * @throws \Exception
     */
    protected static function instance(...$args)
    {
        return self::createFacade('', $args);
    }

    /**
     * 调用类的实例
     * @access public
     * @param  string $class 类名或者标识
     * @param  array|true $args 变量
     * @param  bool $newInstance 是否每次创建新的实例
     * @return object
     * @throws \Exception
     */
    protected static function make($class, $args = [], $newInstance = false)
    {
        if (__CLASS__ != static::class) {
            return self::__callStatic('make', func_get_args());
        }

        if (true === $args) {
            // 总是创建新的实例化对象
            $newInstance = true;
            $args        = [];
        }

        return self::createFacade($class, $args, $newInstance);
    }

    // 调用实际类的方法
    public static function __callStatic($method, $params)
    {
        return call_user_func_array([static::createFacade(), $method], $params);
    }
}
