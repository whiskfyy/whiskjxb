<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
define('SCRIPTNAV', 'article');

if(!$articleid = (int)$_GET['id']) redirect(lang('global_sql_keyid_invalid', 'id'));

$A =& $_G['loader']->model(MOD_FLAG.':article');
if((!$detail = $A->read($articleid)) || $detail['status']!=1) redirect('article_empty');
//投稿权限
$access_post = $user->check_access('article_post', $A, 0);

if($_POST['op'] == 'digg') {
    if($A->digg($articleid)) {
        echo 'OK';
    }
    output();
}

//更新浏览量
$A->pageview($articleid);

//获取主题列表字段
if($detail['sid'] > 0) {
    $I =& $_G['loader']->model('item:subject');
    if(!$subject = $I->read($detail['sid'])) redirect('item_empty');
    $subject_field_table_tr = $I->display_listfield($subject);
}

$_HEAD['keywords'] = ($detail['keywords'] ? (str_replace(" ",",",$detail['keywords']) . ',') : '') . $MOD['meta_keywords'];
$_HEAD['description'] = str_replace("\r\n",',',$detail['introduce']) . $MOD['meta_description'];
if($subject && $subject['templateid'] && $MOD['use_itemtpl']) {
    include template('article_detail', 'item', $subject['templateid']);
} else {
    include template('article_detail');
}
?>