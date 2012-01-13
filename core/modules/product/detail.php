<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
define('SCRIPTNAV', 'product');

if(!$pid = abs(_get('id','0','intval'))) {
	$pid = abs(_get('pid','0','intval'));
}
if(!$pid) redirect(lang('global_sql_keyid_invalid', 'pid'));

$P =& $_G['loader']->model(':product');
$detail = $P->read($pid);
if(!$detail || !$detail['status']) redirect('product_empty');

//生成表格内容
$FD =& $_G['loader']->model('product:fielddetail');
//样式设计
$FD->class = 'key';
$FD->width = '';
$detail_custom_field = '';
$fields = $P->variable('field_' . $detail['modelid']);
foreach($fields as $val) {
    if(in_array($val['fieldname'], array('content'))) continue;
    if($val['show_detail']) {
        $detail_field .= $FD->detail($val, $detail[$val['fieldname']]) . "\r\n";
    }
}

$S =& $_G['loader']->model('item:subject');
if(!$subject = $S->read($detail['sid'])) redirect('item_empty');
$modelid = $S->get_modelid($subject['pid']);

//获取主题列表字段
$subject_field_table_tr = $S->display_listfield($subject);

$urlpath = array();
$urlpath[] = url_path($subject['name'].$subject['subname'], url("item/detail/id/$sid"));
$urlpath[] = url_path(lang('product_title'), url("product/list/sid/$sid"));
$urlpath[] = url_path($detail['subject'], url("product/detail/pid/$pid"));

//更新浏览量
$P->pageview($pid);

///////
$_HEAD['keywords'] = $MOD['meta_keywords'];
$_HEAD['description'] = $MOD['meta_description'];
if($subject && $subject['templateid'] && $MOD['use_itemtpl']) {
    include template('product_detail', 'item', $subject['templateid']);
} else {
    include template('product_detail');
}
?>