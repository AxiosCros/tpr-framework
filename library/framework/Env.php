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

class Env
{
    public static $file_path = '';

    public static $env_array = [];

    public static $instance = null;

    /**
     * 获取环境变量值
     * @param string $name 环境变量名（支持二级 .号分割）
     * @param string $default 默认值
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        $result = getenv(ENV_PREFIX . strtoupper(str_replace('.', '_', $name)));
        if (false !== $result) {
            if ('false' === $result) {
                $result = false;
            } elseif ('true' === $result) {
                $result = true;
            }
            return $result;
        } else {
            return $default;
        }
    }

    public static function config($path)
    {
        if (is_file($path) && self::$file_path != $path) {
            self::$file_path = $path;
            self::$env_array = parse_ini_file(self::$file_path, true);
        }

        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    private static function init()
    {
        if (empty(self::$file_path) && is_file(ROOT_PATH . '.env')) {
            self::$file_path = ROOT_PATH . '.env';
        }
        if (empty(self::$env_array)) {
            self::$env_array = is_file(self::$file_path) ? parse_ini_file(self::$file_path, true) : [];
        }
        return self::$env_array;
    }

    public static function getFromFile($index, $default = null)
    {
        self::init();
        if (strpos($index, '.')) {
            $indexArray = explode('.', $index);
            $envData = self::$env_array;
            $tmp = $envData;
            foreach ($indexArray as $i) {
                $tmp = isset($tmp[$i]) ? $tmp[$i] : null;
                if (is_null($tmp)) {
                    return $default;
                }
            }
        } else {
            $tmp = self::$env_array;
            $tmp = isset($tmp[$index]) ? $tmp[$index] : null;
            if (is_null($tmp)) {
                return $default;
            }
        }
        return $tmp;
    }

    public static function set($index, $value)
    {
        self::init();
        $envArraySection = self::$env_array;
        if (strpos($index, '.')) {
            $indexArray = explode('.', $index);
            $tmpSection = &$envArraySection;
            $tmp = &$envArray;
            $indexLen = count($indexArray);
            foreach ($indexArray as $key => $i) {
                if (!isset($tmpSection[$i])) {
                    return false;
                }
                //final
                if ($key == $indexLen - 1) {
                    $tmpSection[$i] = $value;
                    $tmp[$i] = $value;
                } else {
                    if ($key != 0) {
                        $tmp = &$tmp[$i];
                    }
                    $tmpSection = &$tmpSection[$i];
                }
            }
        } else if (isset(self::$env_array[$index])) {
            $envArraySection[$index] = $value;
        } else {
            return false;
        }

        $name = ENV_PREFIX . strtoupper(str_replace('.', '_', $index));
        putenv("$name=$value");

        self::$env_array = $envArraySection;
        return self::getFromFile($index);
    }

    public static function all()
    {
        return self::init();
    }

    public static function save()
    {
        $envSection = self::$env_array;
        $text = self::envFileString($envSection);
        return file_put_contents(self::$file_path, $text);
    }

    private static function envFileString($data)
    {
        $str = "\r\n";

        foreach ($data as $k1 => $v1) {
            $str .= "[" . $k1 . "]\r\n";
            foreach ($v1 as $k2 => $v2) {
                if (is_array($v2)) {
                    foreach ($v2 as $k3 => $v3) {
                        $str .= $k2 . '[' . $k3 . '] = ' . $v3 . "\r\n";
                    }
                } else {
                    $str .= $k2 . ' = ' . $v2 . "\r\n";
                }
            }
            $str .= "\r\n";
        }
        return $str;
    }

}
