<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/6/28 13:29
 */
namespace think;

class Tool {
    public static $identity;

    public static function uuid($salt=''){
        return md5($salt.uniqid(md5(microtime(true)),true));
    }

    public static function uuidAddFlavour($salt='',$cut=8,$flavour='-',$isUpper=false){
        $str = self::uuid($salt);
        $len = strlen($str);$length = $len;$uuid='';
        if(is_array($cut)){
            while ($length>0){
                $uuid .= substr($str,$len-$length,array_rand($cut)).$flavour;
                $length -=$cut;
            }
        }else if(is_int($cut)){
            $step = 0;
            while ($length>0){
                $temp = substr($str,$len-$length,$cut);
                $uuid .= $step!=0 ? $flavour.$temp:$temp;
                $length -=$cut;
                $step++;
            }
        }
        return $isUpper?strtoupper($uuid):$uuid;
    }

    /**
     * 获取客户端IP地址
     * @param int $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param bool $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    public static function getClientIp($type = 0, $adv = false) {
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if($adv){
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos    =   array_search('unknown',$arr);
                if(false !== $pos) unset($arr[$pos]);
                $ip     =   trim($arr[0]);
            }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip     =   $_SERVER['HTTP_CLIENT_IP'];
            }elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip     =   $_SERVER['REMOTE_ADDR'];
            }
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    public static function identity($identity=false){
        if($identity===false){
            return self::$identity;
        }
        self::$identity = $identity;
        return $identity;
    }

    public static function checkData2String(&$array=[]){
        if(is_object($array)){
            $array = self::object2Array($array);
        }
        if(is_array($array)){
            foreach ($array as &$a){
                if(is_object($a)){
                    $a = self::object2Array($a);
                }
                if(is_array($a)){
                    $a = check_data_to_string($a);
                }
                if(is_int($a)){
                    $a = strval($a);
                }
                if(is_null($a)){
                    $a = "";
                }
            }
        }else if(is_int($array)){
            $array = strval($array);
        }else if(is_null($array)){
            $array = "";
        }
        return $array;
    }

    public static function object2Array($object) {
        $object =  json_decode( json_encode( $object),true);
        return  $object;
    }
}