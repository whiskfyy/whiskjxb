<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$op = $_GET['op'] ? $_GET['op'] : $_POST['op'];
switch($op) {
case 'changepw':
    if($_POST['dosubmit']) {
        $user->change_password($_POST['old'], $_POST['new'], $_POST['new2']);
        redirect('global_op_succeed');
    } else {
        require_once template('changepw','member',MOD_FLAG);
        output();
    }
    break;
case 'change_password':
    if($error = $user->change_password($_POST['old'], $_POST['new'], $_POST['new2'], 1)) {
        echo '<script type="text/javascript">alert("'.$error.'");</script>';
    } else {
        echo '<script type="text/javascript">alert("'.lang('global_op_succeed').'");window.parent.document.forms["changepasswordfrm"].hide.click();</script>';
    }
    output();
    break;
default:

}
?>