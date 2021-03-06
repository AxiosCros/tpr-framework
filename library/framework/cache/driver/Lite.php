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

namespace tpr\framework\cache\driver;

use tpr\framework\cache\Driver;

/**
 * 文件类型缓存类.
 *
 * @author    liu21st <liu21st@gmail.com>
 */
class Lite extends Driver
{
    protected $options = [
        'prefix' => '',
        'path'   => '',
        'expire' => 0, // 等于 10*365*24*3600（10年）
    ];

    /**
     * 构造函数.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        if (DS != substr($this->options['path'], -1)) {
            $this->options['path'] .= DS;
        }
    }

    /**
     * 判断缓存是否存在.
     *
     * @param string $name 缓存变量名
     *
     * @return mixed
     */
    public function has($name)
    {
        return $this->get($name) ? true : false;
    }

    /**
     * 读取缓存.
     *
     * @param string $name    缓存变量名
     * @param mixed  $default 默认值
     *
     * @return mixed
     */
    public function get($name, $default = false)
    {
        $filename = $this->getCacheKey($name);
        if (is_file($filename)) {
            // 判断是否过期
            $mtime = filemtime($filename);
            if ($mtime < $_SERVER['REQUEST_TIME']) {
                // 清除已经过期的文件
                unlink($filename);

                return $default;
            }

            return include $filename;
        }

        return $default;
    }

    /**
     * 写入缓存.
     *
     * @param string $name   缓存变量名
     * @param mixed  $value  存储数据
     * @param int    $expire 有效时间 0为永久
     *
     * @return bool
     */
    public function set($name, $value, $expire = null)
    {
        if (null === $expire) {
            $expire = $this->options['expire'];
        }
        // 模拟永久
        if (0 === $expire) {
            $expire = 10 * 365 * 24 * 3600;
        }
        $filename = $this->getCacheKey($name);
        if ($this->tag && !is_file($filename)) {
            $first = true;
        }
        $ret = file_put_contents($filename, ('<?php return ' . var_export($value, true) . ';'));
        // 通过设置修改时间实现有效期
        if ($ret) {
            isset($first) && $this->setTagItem($filename);
            touch($filename, $_SERVER['REQUEST_TIME'] + $expire);
        }

        return $ret;
    }

    /**
     * 自增缓存（针对数值缓存）.
     *
     * @param string $name 缓存变量名
     * @param int    $step 步长
     *
     * @return false|int
     */
    public function inc($name, $step = 1)
    {
        if ($this->has($name)) {
            $value = $this->get($name) + $step;
        } else {
            $value = $step;
        }

        return $this->set($name, $value, 0) ? $value : false;
    }

    /**
     * 自减缓存（针对数值缓存）.
     *
     * @param string $name 缓存变量名
     * @param int    $step 步长
     *
     * @return false|int
     */
    public function dec($name, $step = 1)
    {
        if ($this->has($name)) {
            $value = $this->get($name) - $step;
        } else {
            $value = $step;
        }

        return $this->set($name, $value, 0) ? $value : false;
    }

    /**
     * 删除缓存.
     *
     * @param string $name 缓存变量名
     *
     * @return bool
     */
    public function rm($name)
    {
        return unlink($this->getCacheKey($name));
    }

    /**
     * 清除缓存.
     *
     * @param string $tag 标签名
     *
     * @return bool
     */
    public function clear($tag = null)
    {
        if ($tag) {
            // 指定标签清除
            $keys = $this->getTagItem($tag);
            foreach ($keys as $key) {
                unlink($key);
            }
            $this->rm('tag_' . md5($tag));

            return true;
        }

        return array_map('unlink', glob($this->options['path'] . ($this->options['prefix'] ? $this->options['prefix'] . DS : '') . '*.php'));
    }

    /**
     * 取得变量的存储文件名.
     *
     * @param string $name 缓存变量名
     *
     * @return string
     */
    protected function getCacheKey($name)
    {
        return $this->options['path'] . $this->options['prefix'] . md5($name) . '.php';
    }
}
