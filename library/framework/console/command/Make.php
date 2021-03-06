<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 刘志淳 <chun@engineer.com>
// +----------------------------------------------------------------------

namespace tpr\framework\console\command;

use tpr\framework\App;
use tpr\framework\Config;
use tpr\framework\console\Command;
use tpr\framework\console\Input;
use tpr\framework\console\input\Argument;
use tpr\framework\console\Output;

abstract class Make extends Command
{
    protected $type;

    abstract protected function getStub();

    protected function configure()
    {
        $this->addArgument('name', Argument::REQUIRED, 'The name of the class');
    }

    /**
     * @param Input  $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('name'));

        $classname = $this->getClassName($name);

        $pathname = $this->getPathName($classname);

        if (is_file($pathname)) {
            $output->writeln('<error>' . $this->type . ' already exists!</error>');
        }

        if (!is_dir(\dirname($pathname))) {
            mkdir(strtolower(\dirname($pathname)), 0755, true);
        }

        file_put_contents($pathname, $this->buildClass($classname));

        $output->writeln('<info>' . $this->type . ' created successfully.</info>');
    }

    protected function buildClass($name)
    {
        $stub = file_get_contents($this->getStub());

        $namespace = trim(implode('\\', \array_slice(explode('\\', $name), 0, -1)), '\\');

        $class = str_replace($namespace . '\\', '', $name);

        return str_replace(['{%className%}', '{%namespace%}', '{%app_namespace%}'], [
            $class,
            $namespace,
            App::$namespace,
        ], $stub);
    }

    protected function getPathName($name)
    {
        $name = str_replace(App::$namespace . '\\', '', $name);

        return APP_PATH . str_replace('\\', '/', $name) . '.php';
    }

    protected function getClassName($name)
    {
        $appNamespace = App::$namespace;

        if (0 === strpos($name, $appNamespace . '\\')) {
            return $name;
        }

        if (Config::get('app_multi_module')) {
            if (strpos($name, '/')) {
                list($module, $name) = explode('/', $name, 2);
            } else {
                $module = 'common';
            }
        } else {
            $module = null;
        }

        if (false !== strpos($name, '/')) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->getNamespace($appNamespace, $module) . '\\' . $name;
    }

    protected function getNamespace($appNamespace, $module)
    {
        return $module ? ($appNamespace . '\\' . $module) : $appNamespace;
    }
}
