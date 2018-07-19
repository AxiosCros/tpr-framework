<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018/7/13 17:24
 */

namespace tpr\framework\traits;

use tpr\framework\Exception;

trait Instance
{
    protected static $instance = null;

    /**
     * @return static
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 静态调用
     * @param $method
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic($method, $params)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        $call = substr($method, 1);
        if (0 === strpos($method, '_') && is_callable([self::$instance, $call])) {
            return call_user_func_array([self::$instance, $call], $params);
        } else {
            throw new Exception("method not exists:" . $method);
        }
    }
}
