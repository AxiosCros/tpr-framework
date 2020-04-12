<?php
/**
 * @author  : Axios
 * @email   : axioscros@aliyun.com
 * @blog    :  http://hanxv.cn
 * @datetime: 2017/7/4 9:12
 */

namespace tpr\framework;

class Crypt
{
    private static $key_path;

    private static $pri_key;

    private static $pub_key;

    public static function path($path = '')
    {
        if (file_exists($path) && is_dir($path)) {
            self::$key_path = $path;
        } elseif (!empty($path)) {
            self::$key_path = CONF_PATH . 'key/' . $path . '/';
        } else {
            return self::$key_path;
        }

        if (!file_exists(self::$key_path)) {
            if (!mkdir(self::$key_path, 0700, true)) {
                return (string) (file_exists(self::$key_path)) . 'Failed to create folders:' . self::$key_path;
            }
        }

        return self::$key_path;
    }

    public static function rsa($path = '')
    {
        self::path($path);
        self::$pri_key = file_exists(self::$key_path . 'pri.pem') ? file_get_contents(self::$key_path . 'pri.pem') : null;
        self::$pub_key = file_exists(self::$key_path . 'pri.pem') ? file_get_contents(self::$key_path . 'pub.pem') : null;

        return new self();
    }

    public static function makeKey($path = '')
    {
        $res = openssl_pkey_new();
        openssl_pkey_export($res, $pri_key);
        $path = empty($path) ? CONF_PATH . 'key/' : $path;
        self::path($path);
        file_put_contents(self::$key_path . 'pri.pem', $pri_key);
        self::$pri_key = $pri_key;

        $res           = openssl_pkey_get_details($res);
        self::$pub_key = $res['key'];
        file_put_contents(self::$key_path . 'pub.pem', self::$pub_key);
    }

    public function encrypt($data, $encrypt = 'pri')
    {
        $str   = '';
        $count = 0;
        for ($i = 0; $i < \strlen($data); $i += 117) {
            $src = substr($data, $i, 117);
            $out = 'pri' == $encrypt ? self::doEncrypt($src, 1) : self::doEncrypt($src, 0);
            if (null === $out) {
                return null;
            }
            $str .= 0 == $count ? base64_encode($out) : ',' . base64_encode($out);
            ++$count;
        }

        return $str;
    }

    public function decrypt($data, $decrypt = 'pri')
    {
        $str = '';
        if (strpos($data, ',')) {
            $dataArray = explode(',', $data);
            foreach ($dataArray as $src) {
                $out = 'pri' == $decrypt ? self::doDecrypt(base64_decode($src), 1) : self::doDecrypt(base64_decode($src), 0);
                if (null === $out) {
                    return null;
                }
                $str .= $out;
            }
        } else {
            $src = base64_decode($data);
            $out = 'pri' == $decrypt ? self::doDecrypt($src, 1) : self::doDecrypt($src, 0);
            if (null === $out) {
                return null;
            }
            $str .= $out;
        }

        return $str;
    }

    private static function doEncrypt($src, $type = 1)
    {
        $rs     = '';
        $result = $type ? @openssl_private_encrypt($src, $rs, self::$pri_key) : @openssl_public_encrypt($src, $rs, self::$pub_key);
        if (false === $result) {
            return null;
        }

        return $rs;
    }

    private static function doDecrypt($src, $type = 1)
    {
        $rs     = '';
        $result = $type ? @openssl_private_decrypt($src, $rs, self::$pri_key) : @openssl_public_decrypt($src, $rs, self::$pub_key);
        if (false === $result) {
            return null;
        }

        return $rs;
    }
}
