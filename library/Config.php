<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018/7/6 16:36
 */

namespace tpr;

use tpr\framework\Facade;

/**
 * Class Config
 * @package tpr
 * @see \tpr\framework\Config
 * @method void setDefaultPrefix($prefix) static 设置配置参数默认前缀
 * @method mixed parse($config, $type = '', $name = '') static 解析配置文件或内容
 * @method mixed load($file, $name = '') static 加载配置文件（多种格式）
 * @method bool has($name) static 检测配置是否存在
 * @method array pull($name) static 获取一级配置
 * @method mixed get($name = null, $default = null) static 获取配置参数 为空则获取所有配置
 * @method mixed set($name, $value = null) static 设置配置参数 name为数组则为批量设置
 * @method mixed remove($name) static 移除配置
 * @method mixed reset($prefix = '') static 重置配置参数
 */
class Config extends Facade
{

}