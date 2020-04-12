<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    :  http://hanxv.cn
 * @datetime: 2018/6/13 16:00
 */

namespace tpr\framework\exception;

use Throwable;
use tpr\framework\Exception;

class PermissionDenied extends Exception
{
    /**
     * PermissionDenied constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param null|Throwable $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
