<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/6/28 13:28
 */

namespace think\cache;

use think\Cache;
use think\Request;

class CacheRequest
{
    public static function set($req, Request $request)
    {
        $config = config('cache', []);
        if (isset($config['list'][$request->path()])) {
            $expire = $config['list'][$request->path()];
            $expire = $expire ? intval($expire) : 300;
            $param = $request->except($config['except_param']);
            $identify = md5($request->path().serialize($param));
            Cache::set($identify, $req, $expire);
        }
    }

    public static function get(Request $request)
    {
        $config = config('cache', []);
        if (isset($config['list'][$request->path()])) {
            $param = $request->except($config['except_param']);
            $identify = md5($request->path().serialize($param));
            $cache = Cache::get($identify);

            return empty($cache) ? false : $cache;
        } else {
            return false;
        }
    }
}
