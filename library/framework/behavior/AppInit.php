<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/6/28 13:12
 */

namespace tpr\framework\behavior;

use tpr\framework\Hook;
use tpr\framework\Tool;

class AppInit
{
    public function run()
    {
        Tool::identity(1);
        if (!IS_CLI) {
            Hook::add('action_begin', 'tpr\\framework\\behavior\\ActionBegin');
            Hook::add('app_end', 'tpr\\framework\\behavior\\AppEnd');
            Hook::add('log_write_done', 'tpr\\framework\\behavior\\LogWriteDone');
            Hook::add('request_end', 'tpr\\framework\\behavior\\RequestEnd');
            Hook::add('response_end', 'tpr\\framework\\behavior\\ResponseEnd');
        }
    }
}
