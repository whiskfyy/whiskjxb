<?php
/**
* 用户登录
* @author moufer<moufer@163.com>
* @package modoer
* @copyright Moufer Studio(www.modoer.com)
*/
!defined('IN_MUDDER') && exit('Access Denied');
define('SCRIPTNAV', 'login');

$op = _get('op');
$forward = $_GET['forward'] ? _T(base64_decode(rawurldecode($_GET['forward']))) : get_forward(url('member/index'));
if(strposex($forward,'logout') || strposex($forward,'login') || !strposex($forward, $_G['web']['domain'])) {
    $forward = $_G['cfg']['siteurl'];
}

switch($op) {
case 'forget':
    if($user->isLogin) redirect('member_login_logined');
    if(check_submit('dosubmit')) {
        $user->forget($_POST['username'], $_POST['email']);
        redirect('member_forget_mail_succeed', url('member/login'));
    } else {
        require_once template('member_forget');
    }
    break;
case 'updatepw':
    if($user->isLogin) redirect('member_login_logined');
    if(check_submit('dosubmit')) {
        $user->update_password((int)$_POST['getpwid'], _T($_POST['secode']), trim($_POST['newpassword']), trim($_POST['newpassword2']));
        redirect('member_getpassword_succeed', url('member/login'));
    } else {
        $getpwid = (int)$_GET['id'];
        $secode = _T($_GET['sec']);
        $uid = $user->check_getpassword($getpwid, $secode);
        if(!$member = $user->read($uid, false, 'uid,username,groupid')) redirect('member_getpassword_username_empty');
        require_once template('member_forget');
    }
    break;
case 'logout':
    $sync = $user->logout();
    redirect(lang('global_op_succeed') . $sync, $forward);
    break;
case 'check':
    if(defined('IN_AJAX')) {
        $search = array('"',"\r\n","\r","\n","\n\r");
        $replace = array('\\"',"{LF}","{LF}","{LF}","{LF}");
        if($user->isLogin) {
            echo '{ type:"login",username:' . '"' . str_replace($search, $replace, $user->username) . '",newmsg:"'.$user->newmsg.'" }';
        } elseif($_G['cookie']['activationauth'] && $_G['cookie']['username']) {
            echo '{ type:"activationauth",username:' . '"' . str_replace($search, $replace, $_G['cookie']['username']) . '" }';
        } else {
            echo '';
        }
        output();
    }
    break;
case 'login':
    if($user->isLogin) redirect('member_login_logined');
	if($user->passport['enable']) location($user->passport['login_url']);
    if(!$_POST['onsubmit']) location(url('member/login'));
    if($MOD['seccode_login']) check_seccode($_POST['seccode']);
    if(!$sync = $user->login($_POST['username'], $_POST['password'], $_POST['life'])) {
        redirect('member_login_lost');
    }
    redirect(lang('member_login_succeed') . $sync, get_forward(url('member/index'),1));
    break;
default:
    if($user->isLogin) redirect('member_login_logined');
	if($user->passport['enable']) location($user->passport['login_url']);
    $subtitle = lang('member_login_title');
    require_once template('member_login');
}
?>
