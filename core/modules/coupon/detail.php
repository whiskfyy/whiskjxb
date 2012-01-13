<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
define('SCRIPTNAV', 'coupon');

if(isset($_GET['id'])) $couponid = (int) $_GET['id'];
if(!$couponid) redirect(lang('global_sql_keyid_invalid','id'));

$CO = $_G['loader']->model(':coupon');
$detail = $CO->read($couponid);
if(!$detail || $detail['status']!=1) redirect('coupon_empty');

if($_GET['do'] == 'effect') {
    $CO->update_effect($couponid, $_POST['effect']);
    echo 'OK';
    exit;
}

if(!$CO->check_valid($couponid, $detail['catid'], $detail['sid'], $detail['endtime'], $detail['status'])) {
    redirect('coupon_status_invalid');
}

$category = $_G['loader']->variable('category',MOD_FLAG);

//更新浏览量
$CO->pageview($couponid);

//获取主题列表字段
if($detail['sid'] > 0) {
    $I =& $_G['loader']->model('item:subject');
    if(!$subject = $I->read($detail['sid'])) redirect('item_empty');
    $subject_field_table_tr = $I->display_listfield($subject);
}

$urlpath = array();
$urlpath[] = url_path($MOD['name'], url("coupon/index"));
$urlpath[] = url_path($category[$detail['catid']]['name'], url("coupon/index/catid/$detail[catid]"));
$urlpath[] = url_path($detail['subject'], url("coupon/detail/id/$couponid"));

$_HEAD['keywords'] = $MOD['meta_keywords'];
$_HEAD['description'] = $MOD['meta_description'];
if($subject && $subject['templateid'] && $MOD['use_itemtpl']) {
    include template('coupon_detail', 'item', $subject['templateid']);
} else {
    include template('coupon_detail');
}
?>