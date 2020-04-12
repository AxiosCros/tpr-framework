<?php
/**
 * @author: axios
 *
 * @email: axiosleo@foxmail.com
 * @blog:  http://hanxv.cn
 * @datetime: 2018/6/6 11:04
 */

namespace tpr\framework\exception;

use Exception;
use tpr\framework\Config;
use tpr\framework\Response;
use tpr\traits\controller\Jump;

class HttpRestException extends Handle
{
    use Jump;

    public function render(Exception $e)
    {
        if (Config::get('app_debug', true)) {
            return parent::render($e);
        }
        $data = [
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'msg'  => $e->getMessage(),
        ];
        $req = [
            'code' => '500',
            'msg'  => 'server error',
            'data' => $data,
            'time' => $_SERVER['REQUEST_TIME'],
        ];

        $return_type = c('default_ajax_return', 'json');
        if (empty($return_type)) {
            $return_type = 'json';
        }
        Response::create($req, $return_type, '500')->send();
        die();
    }
}
