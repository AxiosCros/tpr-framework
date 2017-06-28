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

namespace think;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class Env
{
    public static $file_path = '';

    public static $env_array = [];

    public static $instance = null;

    /**
     * 获取环境变量值
     * @param string    $name 环境变量名（支持二级 .号分割）
     * @param string    $default  默认值
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

    public static function config($path){
        if(is_file($path)){
            self::$file_path = $path;
            self::$env_array = parse_ini_file(self::$file_path, true);
        }else{
            throw new FileNotFoundException($path . ' not found');
        }
        if(is_null(self::$instance)){
            self::$instance = new static();
        }
        return self::$instance ;
    }

    private static function init(){
        if(empty(self::$file_path) && is_file(ROOT_PATH.'.env')){
            self::$file_path = ROOT_PATH.'.env';
        }
        if(empty(self::$env_array)){
            self::$env_array = is_file(self::$file_path) ? parse_ini_file(self::$file_path, true) : [];
        }
    }

    public static function getFromFile($index, $default = null){
        self::init();
        if(strpos($index,'.')){
            $indexArray = explode('.',$index);
            $envData = self::$env_array;
            $tmp = $envData;
            foreach ($indexArray as $i){
                $tmp = isset($tmp[$i])?$tmp[$i]:null;
                if(is_null($tmp)){
                    return $default;
                }
            }
        }else{
            $tmp = self::$env_array;
            $tmp = isset($tmp[$index])?$tmp[$index]:null;
            if(is_null($tmp)){
                return $default;
            }
        }
        return $tmp;
    }


}
