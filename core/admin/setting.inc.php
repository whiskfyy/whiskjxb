<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
(!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied');
$MC =& $_G['loader']->model('config');
$config = $MC->read_all();

$op = $_GET['op'];
if($_POST['dosubmit']) {

    if(isset($_POST['setting']['mail_stmp_password']) && $_POST['setting']['mail_stmp_password'] == '******') {
        unset($_POST['setting']['mail_stmp_password']);
    }

    foreach(array('jsaccess','ban_ip') as $v) {
        if(isset($_POST['setting'][$v])) {
            $_POST['setting'][$v] = preg_replace("/\s*(\r\n|\n\r|\n|\r)\s*/","\r\n",$_POST['setting'][$v]);
        }
    }

    $MC->save($_POST['setting']);
    redirect('global_op_succeed', cpurl($module, $act, $op));

} else {
    $admin->tplname = cptpl('setting_' . $op);
}
?>