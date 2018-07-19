<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018/7/13 17:47
 */

namespace tpr;

use \tpr\framework\Facade;

/**
 * Class Lang
 * @package tpr
 * @method string range($range = '') static
 * @method mixed set($name, $value = null, $range = '') static
 * @method array load($file, $range = '') static
 * @method bool has($name, $range = '') static
 * @method mixed get($name = null, $vars = [], $range = '') static
 * @method string detect() static
 * @method void saveToCookie($lang = null) static
 * @method void setLangDetectVar($var) static
 * @method void setLangCookieVar($var) static
 * @method void setAllowLangList(array $list) static
 * @method void setAcceptLanguage(array $list) static
 */
class Lang extends Facade
{

}