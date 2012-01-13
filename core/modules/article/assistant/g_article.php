<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
$A =& $_G['loader']->model(':article');
$S =& $_G['loader']->model('item:subject');

$subtitle = lang('article_title_g_article');
$role = 'owner';
$_G['loader']->helper('misc',MOD_FLAG);

$mysubjects = $S->mysubject($user->uid);

$status_name = array();
for($i=0;$i<=1;$i++) {
    $status_name[$i] = strip_tags(lang('global_status_'.$i));
}
$status = (int) $_GET['status'] ? $_GET['status'] : 0;
$where = array();
$where['sid'] = $mysubjects;
$where['status'] = $status;
$offset = 20;
$start = get_start($_GET['page'], $offset);
list($total,$list) = $A->find('articleid,subject,catid,comments,pageview,digg,status,dateline',$where,array('dateline'=>'DESC'),$start,$offset,true);
if($total) {
    $multipage = multi($total, $offset, $_GET['page'], url("article/member/ac/$ac/status/$status/page/_PAGE_"));
}
//$status_group = $A->status_total($user->uid);
$access_add = count($mysubjects)>0;
$access_del = $access_add;
$tplname = 'article_list';
?>