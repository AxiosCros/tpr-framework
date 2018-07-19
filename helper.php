<?php
// +----------------------------------------------------------------------
// | TPR [ Design for API development ]
// +----------------------------------------------------------------------
// | Author: AxiosLeo <axiosleo@foxmail.com>
// +----------------------------------------------------------------------
// | Blog: https://hanxu.cn
// +----------------------------------------------------------------------

if(!function_exists('dump')){
    /**
     * @param null $var
     * @param bool $echo
     * @param null $label
     * @param int $flags
     * @return null|string|string[]
     */
    function dump($var = null, $echo = true, $label = null, $flags = ENT_SUBSTITUTE){
        $label = (null === $label) ? '' : rtrim($label) . ':';
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
        $is_cli = PHP_SAPI == 'cli' ? true : false;
        if ($is_cli) {
            $output = PHP_EOL . $label . $output . PHP_EOL;
        } else {
            if (!extension_loaded('xdebug')) {
                $output = htmlspecialchars($output, $flags);
            }
            $output = '<pre>' . $label . $output . '</pre>';
        }
        if ($echo) {
            echo($output);
            return '';
        } else {
            return $output;
        }
    }
}

if (!function_exists('halt')) {
    /**
     * 调试变量并且中断输出
     * @param mixed      $var 调试变量或者信息
     */
    function halt($var)
    {
        dump($var);

        die();
    }
}