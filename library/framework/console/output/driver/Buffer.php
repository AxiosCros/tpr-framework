<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace tpr\framework\console\output\driver;

use tpr\framework\console\Output;

class Buffer
{
    /**
     * @var string
     */
    private $buffer = '';

    public function __construct(Output $output)
    {
        // do nothing
    }

    public function fetch()
    {
        $content      = $this->buffer;
        $this->buffer = '';
        return $content;
    }

    public function write($messages, $newline = false)
    {
        $messages = (array) $messages;

        foreach ($messages as $message) {
            $this->buffer .= $message;
        }
        if ($newline) {
            $this->buffer .= "\n";
        }
    }

    public function renderException(\Exception $e)
    {
        // do nothing
    }

}
