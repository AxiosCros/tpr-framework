<?php
/**
 * @author: Axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/6/28 13:12
 */
namespace think\behavior;

use think\Hook;
use think\Tool;

class AppInit {
    public function run(){
        Tool::identity(1);
        if(!IS_CLI){
            Hook::add('action_begin' ,'think\\behavior\\ActionBegin');
            Hook::add('app_end' ,'think\\behavior\\AppEnd');
            Hook::add('log_write_done', 'think\\behavior\\LogWriteDone');
            Hook::add('request_end', 'think\\behavior\\RequestEnd');
            Hook::add('request_end', 'think\\behavior\\ResponseEnd');
        }
    }
}