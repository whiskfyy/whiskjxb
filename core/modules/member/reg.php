<?php
/**
* 用户注册
* @author moufer<moufer@163.com>
* @copyright Moufer Studio(www.modoer.com)
*/
!defined('IN_MUDDER') && exit('Access Denied');
define('SCRIPTNAV', 'reg');

if($user->isLogin) redirect('member_reg_logined');

$forward = $_GET['forward'] ? $_GET['forward'] : $_G['cfg']['siteurl'];
if(strposex($forward,'op=logout') || !strposex($forward, $_G['web']['domain'])) {
    $forward = $_G['cfg']['siteurl'];
}
$forward = _T(rawurldecode(rawurldecode($forward)));

$_G['loader']->helper('validate');
switch($_GET['op']) {
case 'check_username':
    if(!$username = trim($_POST['username'])) {
        echo lang('member_reg_ajax_name_empty'); exit;
    }
    if($_G['charset'] != 'utf-8') {
        $_G['loader']->lib('chinese', NULL, FALSE);
        $CHS = new ms_chinese('utf-8', $_G['charset']);
        $username = _T($CHS->Convert($username));
    }
    $user->check_username($username, true);
    if($user->check_username_exists($username)) {
        echo lang('member_reg_ajax_name_exists');
        exit;
    }
    echo lang('member_reg_ajax_name_normal'); exit;
    break;
case 'check_email':
    if(!$email = trim($_POST['email'])) {
        echo lang('member_reg_ajax_email_empty'); exit;
    }
    if(!validate::is_email($email)) {
        echo lang('member_reg_ajax_email_invalid'); exit;
    }
    if(!$MOD['existsemailreg'] && $user->check_email_exists($email)) {
        echo lang('member_reg_ajax_email_exists');
        exit;
    }
    echo lang('member_reg_ajax_email_normal'); exit;
    break;
case 'reg':
    break;
default:
    break;
}

if($user->passport['enable']) {
    location($user->passport['reg_url']);
    exit;
}

if($MOD['closereg']) redirect('member_reg_closed');

if($_POST['dosubmit']) {

    if($MOD['seccode_login']) check_seccode($_POST['seccode']);
    $sync = $user->register($user->get_post($_POST));
    redirect(lang('member_reg_succeed') . $sync, $forward);

} else {

    $subtitle = lang('member_reg_title');
    require_once template('member_reg');
		
}
?>