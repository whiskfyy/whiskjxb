<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

//实例化点评类
$R =& $_G['loader']->model(':review');
define('SCRIPTNAV', 'review');

if(!$rid = _get('id',0,MF_INT_KEY)) redirect(lang('global_sql_keyid_invalid','id'));
if(!$detail = $R->read($rid,'r.*,m.groupid,m.point',true)) redirect('review_empty');

//added by whisk start
$detail['subject'] = MaskIdNo($detail['subject']);
//added by whisk end
$taggroups = $_G['loader']->variable('taggroup','item');

$typeinfo = $R->get_type($detail['idtype']);
$OBJ =& $_G['loader']->model($typeinfo['model_name']);
if($object = $OBJ->read($detail['id'])) {
    $object_field_table_tr = $OBJ->display_listfield($object);
}
//added by whisk start
$object['name'] = MaskIdNo($object['name']);
//added by whisk end
//回应
$RP =& $_G['loader']->model(MOD_FLAG.':respond');
$select = 'respondid,rid,uid,username,content,posttime';
$where = array ( 'rid' => $rid, 'status' => 1 );
$offset = $MOD['respond_num'] > 0 ? $MOD['respond_num'] : 10;
$start = get_start($_GET['page'], $offset);
$orderby = array('posttime' => 'DESC');
list($total, $responds) = $RP->find($select, $where, $orderby, $start, $offset);
if($total) {
    $multipage = multi($total, $offset, $_GET['page'], url("review/detail/id/$rid/page/_PAGE_"));
}

$urlpath = array();
$urlpath[] = url_path($MOD['name'], url("review/index"));
$urlpath[] = url_path($detail['subject'], url(str_replace('_ID_',$detail['id'],$typeinfo['detail_url'])));

$_HEAD['keywords'] = $detail['username'].','.$detail['title'].','.$detail['subject'];
$_HEAD['description'] = trimmed_title(str_replace("\r\n",",",$detail['content']),100);

include template('review_detail');
?>