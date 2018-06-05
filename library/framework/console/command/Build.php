<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace tpr\framework\console\command;

use tpr\framework\console\Command;
use tpr\framework\console\Input;
use tpr\framework\console\input\Option;
use tpr\framework\console\Output;

class Build extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('build')
            ->setDefinition([
                new Option('config', null, Option::VALUE_OPTIONAL, "build.php path"),
                new Option('module', null, Option::VALUE_OPTIONAL, "module name"),
            ])
            ->setDescription('Build Application Dirs');
    }

    protected function execute(Input $input, Output $output)
    {
        if ($input->hasOption('module')) {
            \tpr\framework\Build::module($input->getOption('module'));
            $output->writeln("Successed");
            return;
        }

        if ($input->hasOption('config')) {
            $build = include $input->getOption('config');
        } else {
            $build = include APP_PATH . 'build.php';
        }
        if (empty($build)) {
            $output->writeln("Build Config Is Empty");
            return;
        }
        \tpr\framework\Build::run($build);
        $output->writeln("Successed");

    }
}
