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
use tpr\framework\Request;

class CaptchaController
{
    public function index()
    {
        $captcha = new Captcha((array)Config::get('captcha'));
        $id = Request::instance()->get('id');
        return $captcha->entry($id);
    }
}