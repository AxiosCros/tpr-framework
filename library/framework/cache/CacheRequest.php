<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/6/28 13:28
 */

namespace tpr\framework\cache;

use tpr\framework\Cache;
use tpr\framework\Request;

class CacheRequest
{
    public static function set($req, Request $request)
    {
        if (!empty($req)) {
            $path   = $request->path();
            $expire = c('cache.' . $path, null);
            $except = c('cache.except_param', ['token', 'sign', 'timestamp']);
            if (\is_int($expire)) {
                $param    = $request->except($except);
                $identify = self::identify($path, $param);
                Cache::set($identify, $req, $expire);
            }
        }
    }

    public static function get(Request $request)
    {
        $path   = $request->path();
        $expire = c('cache.' . $path, null);
        $except = c('cache.except_param', ['token', 'sign', 'timestamp']);
        if (\is_int($expire)) {
            $param    = $request->except($except);
            $identify = self::identify($path, $param);
            $cache    = Cache::get($identify);

            return empty($cache) ? false : $cache;
        }

        return false;
    }

    private static function identify($path, $param = [])
    {
        return md5($path . serialize($param));
    }
}
