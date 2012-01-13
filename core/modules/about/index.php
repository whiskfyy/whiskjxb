<?php
/**
* @author moufer<moufer@163.com>
* @pageage space
* @copyright Moufer Studio(www.modoer.com)
*/
!defined('IN_MUDDER') && exit('Access Denied');
define('SCRIPTNAV', 'about_index');

$uid = $_GET['uid'] > 0 ? $_GET['uid'] : ($_GET['suid'] ? $_GET['suid'] : $user->uid);
if(!$uid) {
    if($username = _T(_get('username'))) {
        if($member = $user->read($username,TRUE)) {
            $uid = $member['uid'];
        }
    }
    if(!$uid){
        //redirect(lang('global_sql_keyid_invalid', 'uid'));
        //载入模型的内容页模板
        print_r("ok");
        //include template('about_index');
        exit;
    }
}

//跳转
if(defined('IN_UC')) {
    $ucfg = $_G['loader']->variable('config','ucenter');
    if($ucfg['uc_uch'] &&$ucfg['uc_uch_url']) {
        header("Location:".$ucfg['uc_uch_url']."/?".$uid);
    }
}

if(!isset($member) || !is_array($member)) {
    $member = $user->read($uid);
}

$SA =& $_G['loader']->model(':space');
if(!$space = $SA->read($uid)) {
    //redirect('space_not_exists');
    $SA->create($uid, $member['username']);
    $space = $SA->read($uid);
}

//更新浏览量
$SA->pageview($uid);

$_HEAD['description'] = $space['spacename'] . ',' . $space['spacedescribe'];

if($templateid = _get('templateid',null,MF_INT_KEY)) {
    $space['about_styleid'] = $templateid;
}
if($space['about_styleid']) {
    include template('about_index', 'about', $space['about_styleid']);
} else {
    //载入模型的内容页模板
    include template('about_index');
}
?>