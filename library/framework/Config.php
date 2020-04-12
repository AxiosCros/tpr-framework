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

namespace tpr\framework;

class Config
{
    // 配置参数
    private static $config = [];
    // 参数作用域
    private static $range = '_sys_';

    // 设定配置参数的作用域
    public static function range($range)
    {
        self::$range = $range;
        if (!isset(self::$config[$range])) {
            self::$config[$range] = [];
        }
    }

    /**
     * 解析配置文件或内容.
     *
     * @param string $config 配置文件路径或内容
     * @param string $type   配置解析类型
     * @param string $name   配置名（如设置即表示二级配置）
     * @param string $range  作用域
     *
     * @return mixed
     */
    public static function parse($config, $type = '', $name = '', $range = '')
    {
        $range = $range ?: self::$range;
        if (empty($type)) {
            $type = pathinfo($config, PATHINFO_EXTENSION);
        }
        $class = false !== strpos($type, '\\') ? $type : '\\tpr\\framework\\config\\driver\\' . ucwords($type);
        /** @var \tpr\framework\config\driver\Ini $Class */
        $Class = new $class();

        return self::set($Class->parse($config), $name, $range);
    }

    /**
     * 加载配置文件（PHP格式）.
     *
     * @param string $file  配置文件名
     * @param string $name  配置名（如设置即表示二级配置）
     * @param string $range 作用域
     *
     * @return mixed
     */
    public static function load($file, $name = '', $range = '')
    {
        $range = $range ?: self::$range;
        if (!isset(self::$config[$range])) {
            self::$config[$range] = [];
        }
        if (is_file($file)) {
            $name = strtolower($name);
            $type = pathinfo($file, PATHINFO_EXTENSION);
            if ('php' == $type) {
                return self::set(include $file, $name, $range);
            }
            if ('yaml' == $type && \function_exists('yaml_parse_file')) {
                return self::set(yaml_parse_file($file), $name, $range);
            }

            return self::parse($file, $type, $name, $range);
        }

        return self::$config[$range];
    }

    /**
     * 检测配置是否存在.
     *
     * @param string $name  配置参数名（支持二级配置 .号分割）
     * @param string $range 作用域
     *
     * @return bool
     */
    public static function has($name, $range = '')
    {
        $range = $range ?: self::$range;

        if (!strpos($name, '.')) {
            return isset(self::$config[$range][strtolower($name)]);
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name, 2);

        return isset(self::$config[$range][strtolower($name[0])][$name[1]]);
    }

    /**
     * 获取配置参数 为空则获取所有配置.
     *
     * @param string $name  配置参数名（支持二级配置 .号分割）
     * @param string $range 作用域
     *
     * @return mixed
     */
    public static function get($name = null, $range = '')
    {
        $range = $range ?: self::$range;
        // 无参数时获取所有
        if (empty($name) && isset(self::$config[$range])) {
            return self::$config[$range];
        }

        if (!strpos($name, '.')) {
            $name = strtolower($name);

            return isset(self::$config[$range][$name]) ? self::$config[$range][$name] : null;
        }
        // 二维数组设置和获取支持
        $name    = explode('.', $name, 2);
        $name[0] = strtolower($name[0]);

        return isset(self::$config[$range][$name[0]][$name[1]]) ? self::$config[$range][$name[0]][$name[1]] : null;
    }

    /**
     * 设置配置参数 name为数组则为批量设置.
     *
     * @param array|string $name  配置参数名（支持二级配置 .号分割）
     * @param mixed        $value 配置值
     * @param string       $range 作用域
     *
     * @return mixed|\tpr\framework\config\driver\Json
     */
    public static function set($name, $value = null, $range = '')
    {
        $range = $range ?: self::$range;
        if (!isset(self::$config[$range])) {
            self::$config[$range] = [];
        }
        if (\is_string($name)) {
            if (!strpos($name, '.')) {
                self::$config[$range][strtolower($name)] = $value;
            } else {
                // 二维数组设置和获取支持
                $name                                                 = explode('.', $name, 2);
                self::$config[$range][strtolower($name[0])][$name[1]] = $value;
            }

            return '';
        }
        if (\is_array($name)) {
            // 批量设置
            if (!empty($value)) {
                self::$config[$range][$value] = isset(self::$config[$range][$value]) ?
                    array_merge(self::$config[$range][$value], $name) :
                    self::$config[$range][$value] = $name;

                return self::$config[$range][$value];
            }

            return self::$config[$range] = array_merge(self::$config[$range], array_change_key_case($name));
        }
        // 为空直接返回 已有配置
        return self::$config[$range];
    }

    /**
     * 重置配置参数.
     *
     * @param string $range
     */
    public static function reset($range = '')
    {
        $range = $range ?: self::$range;
        if (true === $range) {
            self::$config = [];
        } else {
            self::$config[$range] = [];
        }
    }
}
