<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
define('SCRIPTNAV', 'exchange');

$GT =& $_G['loader']->model(MOD_FLAG.':gift');
$select = 'giftid,name,price,num,pageview,thumb,salevolume';
$where = array('available'=>1);
$start = get_start($_GET['page'],$offset = 20);
list($total,$list) = $GT->find($select, $where, 'displayorder', $start, $offset, true);
if($total) {
    $multipage = multi($total, $offset, $_GET['page'], url("exchange/index/page/_PAGE_"));
}

$_HEAD['keywords'] = $MOD['meta_keywords'];
$_HEAD['description'] = $MOD['meta_description'];
include template('exchange_index');
?>