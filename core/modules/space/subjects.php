<?php
/**
* @author moufer<moufer@163.com>
* @pageage space
* @copyright Moufer Studio(www.modoer.com)
*/
!defined('IN_MUDDER') && exit('Access Denied');
define('SCRIPTNAV', 'space_subjects');

$uid = $_GET['uid'] > 0 ? $_GET['uid'] : ($_GET['suid'] ? $_GET['suid'] : $user->uid);
if(!$uid) redirect(lang('global_sql_keyid_invalid', 'uid'));

$SA =& $_G['loader']->model(':space');
$space = $SA->read($uid);
$member = $user->read($uid);

// 添加的主题
$S =& $_G['loader']->model('item:subject');
$where = array();
$where['cuid'] = $uid;
$where['status'] = 1;
$offset = $MOD['subjects'] > 0 ? $MOD['subjects'] : 20;
$start = get_start($_GET['page'], $offset);
list($total, $subjects) = $S->find('sid,name,subname,pid,catid,avgsort,reviews,pictures,thumb,addtime', $where, array('addtime' => 'DESC'), $start, $offset, TRUE);
if($total) {
    $multipage = multi($total, $offset, $_GET['page'], url("space/subjects/uid/$uid/page/_PAGE_"));
}

$_HEAD['description'] = $space['spacename'] . ',' . $space['spacedescribe'];

if($space['space_styleid']) {
    include template('space_subjects', 'space', $space['space_styleid']);
} else {
    //载入模型的内容页模板
    include template('space_subjects');
}
?>