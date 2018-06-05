<?php
/**
 * @author: axios
 *
 * @email: axiosleo@foxmail.com
 * @blog:  http://hanxv.cn
 * @datetime: 2018/6/5 13:47
 */
namespace tpr\controller;

use tpr\framework\Captcha;
use tpr\framework\Config;

class CaptchaController
{
    public function index($id = "")
    {
        $captcha = new Captcha((array)Config::get('captcha'));
        return $captcha->entry($id);
    }
}