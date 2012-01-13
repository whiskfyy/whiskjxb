<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
define('SCRIPTNAV', 'announcement');

$ANN = $_G['loader']->model('announcement');

switch($_GET['do']) {
case 'list':
    $offset = 40;
    $start = get_start($_GET['page'],$offset);
    list($total,$list) = $ANN->find('id,title,orders,author,dateline,available',array('available'=>'1'),array('orders'=>'ASC','dateline'=>'DESC'),$start,$offset,true);
    if($total) $multipage = multi($total, $offset, $_GET['page'], url('index/announcement/do/list/page/_PAGE_'));
    break;
default:
    $id = _get('id', null, MF_INT_KEY);
    if(!$detail = $ANN->read($id)) redirect('global_ann_empty');
}

include template('modoer_announcement');
?>