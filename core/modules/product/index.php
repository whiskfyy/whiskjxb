<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
define('SCRIPTNAV', 'product');

$P =& $_G['loader']->model(':product');
$category = $_G['loader']->variable('category','item');

$urlpath = array();
$urlpath[] = url_path($MOD['name'], url("product/index"));

$where = array();
$catid = abs(_get('catid',0,'intval'));
if($catid && isset($category[$catid])) {
	$where['s.pid'] = $catid;
	$urlpath[] = url_path($category[$catid]['name'], url("product/index/catid/$catid"));
} else {
	if(!$catid) unset($catid);
}
$keyword = _get('keyword','','_T');
if($keyword) {
	$where['subject'] = array('where_like',array("%$keyword%"));
	$urlpath[] = url_path($keyword, url("product/index/catid/$catid/keyword/$keyword"));
}
$where['p.status'] = 1;
$orderby = _cookie('list_display_product_orderby','','_T');
$orderby != 'pageview' && $orderby = 'pid';
$offset = 20;
$start = get_start($_GET['page'], $offset);
list($total, $list) = $P->find('p.*',$where,array('p.'.$orderby=>'DESC'),$start,$offset,TRUE,'s.name,s.subname');
if($total) $multipage = multi($total, $offset, $_GET['page'], url("product/index/catid/$catid/keyword/$keyword/page/_PAGE_"));

$view = _cookie('list_display_product_view','normal','_T');
$active = array();
$catid && $active['catid'][$catid] = ' class="selected"';
$active['view'][$view] = ' class="selected"';
$active['orderby'][$orderby] = ' class="selected"';

$_HEAD['keywords'] = $MOD['meta_keywords'];
$_HEAD['description'] = $MOD['meta_description'];
include template('product_index');
?>