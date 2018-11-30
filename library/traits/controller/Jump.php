<?php

/**
 * 用法：
 * load_trait('controller/Jump');
 * class index
 * {
 *     use \traits\controller\Jump;
 *     public function index(){
 *         $this->error();
 *         $this->redirect();
 *     }
 * }
 */

namespace tpr\traits\controller;

use tpr\framework\Config;
use tpr\framework\exception\HttpResponseException;
use tpr\framework\Request;
use tpr\framework\Response;
use tpr\framework\response\Redirect;
use tpr\framework\Tool;
use tpr\framework\Url;
use tpr\framework\View as ViewTemplate;

trait Jump
{
    private $return_type;

    private $return_data = [];

    private $headers = [];

    private $options = [];

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     *
     * @param mixed   $msg  提示信息
     * @param string  $url  跳转的URL地址
     * @param mixed   $data 返回的数据
     * @param integer $wait 跳转等待时间
     *
     * @throws \tpr\framework\Exception
     */
    protected function success($msg = 'success', $url = null, $data = '', $wait = 3)
    {
        $code = 1;
        if (is_numeric($msg)) {
            $code = $msg;
            $msg  = '';
        }
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
        } elseif ('' !== $url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : Url::build($url);
        }

        $this->setResult('code', $code)
            ->setResult('msg', $this->msg($msg))
            ->setResult('data', $data)
            ->setResult('url', $url)
            ->setResult('wait', $wait);

        $result = $this->viewResult('dispatch_success_tmpl');
        $this->result($result);
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     *
     * @param mixed   $msg  提示信息
     * @param string  $url  跳转的URL地址
     * @param mixed   $data 返回的数据
     * @param integer $wait 跳转等待时间
     *
     * @return void
     * @throws \tpr\framework\Exception
     */
    protected function error($msg = 'error', $url = null, $data = '', $wait = 3)
    {
        $code = 0;
        if (is_numeric($msg)) {
            $code = $msg;
            $msg  = '';
        }
        if (is_null($url)) {
            $url = Request::instance()->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ('' !== $url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : Url::build($url);
        }

        $this->setResult('code', $code)
            ->setResult('msg', $this->msg($msg))
            ->setResult('data', $data)
            ->setResult('url', $url)
            ->setResult('wait', $wait);

        $result = $this->viewResult('dispatch_error_tmpl');
        $this->result($result);
    }

    /**
     * @param $default_tpl
     *
     * @return array
     * @throws \tpr\framework\Exception
     */
    private function viewResult($default_tpl)
    {
        $this->return_type = $this->getResponseType();
        if ('html' == strtolower($this->return_type)) {
            $result = ViewTemplate::instance(Config::get('template'), Config::get('view_replace_str'))
                ->fetch(Config::get($default_tpl), $this->return_data);
        } else {
            $result = $this->return_data;
        }

        return $result;

    }

    /**
     * 设置回调数据
     *
     * @param        $key
     * @param string $value
     *
     * @return $this
     */
    protected function setResult($key, $value = '')
    {
        $this->return_data[$key] = $value;
        return $this;
    }

    /**
     * 设置响应头
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    protected function setHeader($key = '', $value = '')
    {
        if (is_array($key)) {
            $this->headers = array_merge($this->headers, $key);
        } else {
            $this->headers[$key] = $value;
        }
        return $this;
    }

    /**
     * 获取已设置的响应头
     *
     * @param string $key
     *
     * @return array|null
     */
    protected function getHeaders($key = '')
    {
        if (!empty($key)) {
            return isset($this->headers[$key]) ? $this->headers[$key] : null;
        }
        return $this->headers;
    }

    /**
     * 设置当前 response 输出类型
     *
     * @param $return_type
     *
     * @return $this
     */
    protected function setResponseType($return_type)
    {
        $this->return_type = $return_type;
        return $this;
    }

    /**
     * 设置配置参数
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    protected function setOptions($key = '', $value = '')
    {
        if (is_array($key)) {
            $this->options = array_merge($this->options, $key);
        } else {
            $this->options[$key] = $value;
        }
        return $this;
    }

    /**
     * URL重定向
     * @access protected
     *
     * @param string        $url    跳转的URL表达式
     * @param array|integer $params 其它URL参数
     * @param integer       $code   http code
     * @param array         $with   隐式传参
     *
     * @throws \tpr\framework\exception\PermissionDenied
     */
    protected function redirect($url, $params = [], $code = 302, $with = [])
    {
        $response = new Redirect($url);
        if (is_integer($params)) {
            $code   = $params;
            $params = [];
        }
        $response->code($code)->params($params)->with($with);
        throw new HttpResponseException($response);
    }

    /**
     * 获取当前的 response 输出类型
     * @access protected
     * @return string
     * @throws \tpr\framework\Exception
     */
    protected function getResponseType()
    {
        if (!empty($this->return_type)) {
            return $this->return_type;
        }
        $isAjax            = Request::instance()->isAjax();
        $this->return_type = $isAjax ? c('default_ajax_return', 'json') : c('default_return_type', 'html');
        return $this->return_type;
    }

    /**
     * 异常情况下的回调
     *
     * @param int    $code
     * @param string $message
     */
    protected function wrong($code = 500, $message = '')
    {
        $this->response([], $code, $message);
    }

    /**
     * 正常情况下的数据回调
     *
     * @param array  $data
     * @param int    $code
     * @param string $message
     */
    protected function response($data = [], $code = 200, $message = 'success')
    {
        if ($code != 200 && empty($message)) {
            $message = c('code.' . strval($code), '');
        }

        $this->setResult('code', $code)
            ->setResult('msg', $this->msg($message))
            ->setResult('time', $_SERVER['REQUEST_TIME'])
            ->setResult('data', $data);

        $result = $this->return_data;
        $result = Tool::checkData2String($result);
        $this->result($result);
    }

    /**
     * 返回封装后的API数据到客户端
     * @access protected
     *
     * @param mixed $result 要返回的数据
     * @param array $header
     */
    protected function result($result, $header = [])
    {
        $type = empty($this->return_type) ? c('default_ajax_return', 'json') : $this->return_type;
        $this->setHeader($header);
        $response = Response::create($result, $type)->options($this->options)->header($this->getHeaders());
        throw new HttpResponseException($response);
    }

    /**
     * 无处理回调
     *
     * @param       $output
     * @param array $header
     */
    protected function output($output = null, $header = [])
    {
        $this->setHeader($header);
        $response = Response::create($output, "text")->options($this->options)->header($this->getHeaders());
        throw new HttpResponseException($response);
    }

    protected function getReturnData(){
        return $this->return_data;
    }

    /**
     * 设置回调信息,多语言翻译
     *
     * @param string $message
     *
     * @return mixed|string
     */
    private function msg($message = '')
    {
        if (!empty($message)) {
            $message = lang($message);
        }

        if (!is_string($message)) {
            $message = '';
        }

        return $message;
    }
}
