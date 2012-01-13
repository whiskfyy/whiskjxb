<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
define('SCRIPTNAV', 'item');

$catid = isset($_GET['catid']) ? (int)$_GET['catid'] : (int)$MOD['pid'];
!$catid and redirect('item_empty_default_pid');

//实例化主题类
$I =& $_G['loader']->model('item:subject');
$I->get_category($catid);
if(!$pid = $I->category['catid']) {
    redirect('item_cat_empty');
}

//载入配置信息
$catcfg =& $I->category['config'];
$modelid = $I->category['modelid'];
$rogid = $I->category['review_opt_gid'];

//载入模型
$model = $I->variable('model_' . $modelid);
//载入点评选项
$reviewpot = $_G['loader']->variable('opt_' . $rogid, 'review');

//分类分级变量1主2子
$category = $_G['loader']->variable('category_' . $pid, 'item');
//判断子分类是否禁用
if(!$category[$catid]['enabled']) redirect('item_cat_disabled');
//当前catid的级别
$category_level = $category[$catid]['level'];
$urlpath = array();
$urlpath[] = url_path($category[$pid]['name'], url("item/list/catid/$pid"));
if($catid != $pid) {
    if($category_level > 2) {
        $urlpath[] = url_path($category[$category[$catid]['pid']]['name'], url("item/list/catid/{$category[$catid]['pid']}"));
    }
    $urlpath[] = url_path($category[$catid]['name'], url("item/list/catid/$catid"));
}
$subcats = null;
if($category_level<=2) {
	foreach($category as $__v) {
		if($__v['pid']==$catid) {
			$subcats[] = $__v['catid'];
		}
	}
	unset($__v);
}
//兼容以前的版本
$pcat =& $category;
$where = array();
//使用了地图功能
if($model['usearea']) {

	$aid = (int) $_GET['aid'];

	//载入地区
	$area = $_G['loader']->variable('area_1');
    //地区级别
    $area_level = $area[$aid]['level'];

    if($area_level == 2) {
        $paid = 0;
    } else {
        $paid = $area[$aid]['pid'];
    }

    if($paid) {
        $urlpath[] = url_path($area[$paid]['name'], url("item/list/catid/$pid/aid/$paid"));
    }
    if($paid != $aid) {
        $urlpath[] = url_path($area[$aid]['name'], url("item/list/catid/$pid/aid/$aid"));
    }

	$boroughs = $streets = '';
	foreach($area as $key => $val) {
		if($val['level'] == 2) $boroughs[$key] = $val['name'];
		if($val['level'] == 3 && ($aid==$val['pid']||$paid==$val['pid'])) $streets[$key] = $val['name'];
	}
}
//属性组处理
$atts = array(); 
if($att = _get('att',null,'_T')) {
    $attp = $att;
    $att = explode('_', $att);
    foreach($att as $att_v) {
        list($att_catid, $att_id) = explode('.', $att_v);
        if(!$att_catid || !$att_id) continue;
        $atts[$att_catid] = $att_id;
    }
}
$atturl = item_att_url();

if($aid) $where['aid'] = (int) $aid;
if($catid != $pid) {
    $where['catid'] = array_merge((array)$catid, $I->get_sub_catids($catid));
} else {
    $where['pid'] = (int) $pid;
}
$where['status'] = 1;

// 显示数量
$numlist = array (10, 20, 40);
// 显示方式
$typelist = lang('item_list_displytype');
$type = _cookie('list_display_item_subject_type', $catcfg['displaytype'], '_T');
$type = isset($typelist[$type]) ? $type : 'normal';
// 排序数组
$orderlist = lang('item_list_orderlist');

//查询的数组
$order_arr  = array(
    'finer' => array('finer'=>'DESC'),
    'avgsort' => array('avgsort'=>'DESC'),
    'reviews' => array('reviews'=>'DESC'),
    'enjoy' => array('best'=>'DESC'),
    'price' => array('avgprice'=>'DESC'),
    'price_s' => array('avgprice'=>'ASC'),
    'picture' => array('pictures'=>'DESC'),
    'picture_s' => array('pictures'=>'ASC'),
    'addtime' => array('addtime'=>'DESC'),
    'pageviews' => array('pageviews'=>'DESC'),
);

if($catcfg['useprice']) {
    $orderlist['price'] = $catcfg['useprice_title'];
}
$orderby = _cookie('list_display_item_subject_orderby',$catcfg['listorder'],'_T');
if(!$orderby || !isset($order_arr[$orderby])) {
	$orderby = $catcfg['listorder'];
}

$fields = $I->variable('field_' . $modelid);
$select = 's.sid,pid,catid,domain,name,avgsort,sort1,sort2,sort3,sort4,sort5,sort6,sort7,sort8,best,finer,pageviews,reviews,pictures,favorites,thumb';
if($model['usearea']) {
    if(false == strpos($select, 'aid')) $select .= ',aid';
    if(false == strpos($select, 'map_lat')) $select .= ',map_lat';
    if(false == strpos($select, 'map_lng')) $select .= ',map_lng';
}
if($catcfg['useprice']) {
    if(false == strpos($select, 'avgprice')) $select .= ',avgprice';
}
$select_arr = explode(',', $select);
$custom_field = array();
foreach($fields as $val) {
    if(!$val['show_list']) continue;
	$ver_field = array('mappoint');
    if(!in_array($val['fieldname'], $select_arr) && !in_array($val['fieldname'], $ver_field)) {
        $select .= ',' . $val['fieldname'];
    }
    if(!in_array($val['fieldname'], array('name','subname','mappoint','owner','status','templateid','listorder'))) {
        $custom_field[] = $val;
    }
}
unset($select_arr, $val);

$num = abs(_cookie('list_display_item_subject_num', (int)$MOD['list_num'], 'intval'));
(!$num || !in_array($num, $numlist)) && $num = 20;
$start = get_start($_GET['page'], $num);

if($total = _get('total',null,MF_INT)) {
	list(, $list) = $I->find($select, $where, $order_arr[$orderby], $start, $num, false, $pid, $atts);
} else {
	list($total, $list) = $I->find($select, $where, $order_arr[$orderby], $start, $num, TRUE, $pid, $atts);
}
if($total) {
    $multipage = multi($total, $num, $_GET['page'], url("item/list/catid/$catid/aid/$aid/order/$order/type/$type/att/$atturl/num/$num/total/$total/page/_PAGE_"));
}
//added by whisk start
$resList = array();
if ($total){
    while($res = $list->fetch_array()){
        $subject = $res['name'];
        $res['name'] = MaskIdNo($subject);
        $resList[] = $res;
}
}
//added by whisk end
if($total) {
    //内容
    $FD =& $_G['loader']->model(MOD_FLAG.':fielddetail');
    //样式设计
    $FD->td_num = 1; //表只有1个td
    $FD->class = "";
    $FD->width = "";
    $FD->align = "left";
}

define('SUBJECT_CATID', $catid);

$active = array();
$active['type'][$type] = ' class="selected"';
$active['num'][$num] = ' class="selected"';
$active['orderby'][$orderby] = ' class="selected"';

// 最近的浏览COOKIE
$cookie_subjects = $I->read_cookie($pid);

$_HEAD['keywords'] = $catcfg['meta_keywords'];
$_HEAD['description'] = $catcfg['meta_description'];
include template($model['tplname_list']);

function item_att_url($catid=null, $attid=null, $del=false) {
    global $atts;
    $myatts = $atts;
    if($catid) {
        if($del) {
            unset($myatts[$catid]);
        } else {
            $myatts[$catid] = $attid;
        }
    }
    $url = $split = '';
    foreach($myatts as $catid=>$attid) {
        $url .= $split . $catid .'.'.$attid;
        $split = '_';
    }
    return $url;
}
?>