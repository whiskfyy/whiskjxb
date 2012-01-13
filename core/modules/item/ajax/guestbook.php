<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$allow_ops = array( 'get', 'post', 'edit', 'delete', 'reply', 'insert_reply' );
$login_ops = array( 'post', 'edit', 'delete', 'reply' );

$op = empty($op) || !in_array($op, $allow_ops) ? '' : $op;

if(!$op) {
    redirect('global_op_unkown');
} elseif(!$user->isLogin && in_array($op, $login_ops)) {
    redirect('member_not_login');
}

$GB =& $_G['loader']->model(MOD_FLAG.':guestbook');

switch($op) {

case 'get':

    if(!$sid = (int)$_GET['sid']) redirect(lang('global_sql_keyid_invalid', 'sid'));
    $S =& $_G['loader']->model(MOD_FLAG.':subject');
    if(!$detail = $S->read($sid,'pid,catid,name,subname,owner,status', FALSE)) redirect('item_empty');
    $is_owner = $user->isLogin && $user->username == $detail['owner'];

    $where = array();
    $where['sid'] = $sid;
    $where['status'] = 1;
    $orderby = array('dateline'=>'DESC');
    $offset = $MOD['guestbook_num'] > 0 ? $MOD['guestbook_num'] : 10;
    $start = get_start($_GET['page'], $offset);

    list($total, $guestbooks) = $GB->find($select, $where, $orderby, $start, $offset);
    if($total) {
        $onclick = "get_guestbook($sid, {PAGE})";
        $multipage = multi($total, $offset, $_GET['page'], '','', $onclick);
    }
    include template('item_subject_detail_guestbook');
    output();
    break;

default:

    redirect('global_op_unkown');

}
?>