<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace tpr\framework\console\command;

use tpr\framework\console\Command;
use tpr\framework\console\Input;
use tpr\framework\console\input\Option;
use tpr\framework\console\Output;

class Clear extends Command
{
    protected function configure()
    {
        // 指令配置
        $this
            ->setName('clear')
            ->addOption('path', 'd', Option::VALUE_OPTIONAL, 'path to clear', null)
            ->setDescription('Clear runtime file');
    }

    protected function execute(Input $input, Output $output)
    {
        $path = $input->getOption('path') ?: RUNTIME_PATH;

        if (is_dir($path)) {
            $this->clearPath($path);
        }

        $output->writeln("<info>Clear Successed</info>");
    }

    protected function clearPath($path)
    {
        $path  = realpath($path) . DS;
        $files = scandir($path);
        if ($files) {
            foreach ($files as $file) {
                if ('.' != $file && '..' != $file && is_dir($path . $file)) {
                    $this->clearPath($path . $file);
                } elseif ('.gitignore' != $file && is_file($path . $file)) {
                    unlink($path . $file);
                }
            }
        }
    }
}
