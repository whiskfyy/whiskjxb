<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
define('SCRIPTNAV', 'item');

$I =& $_G['loader']->model(MOD_FLAG.':subject');
//随便看看
if(_get('random',null)) {
    $where = array();
    if(!$detail = $I->read_random($where)) redirect('item_empty');
    location(url("item/detail/id/$detail[sid]"));
}

if($sid = (int) $_GET['sid']) {
    unset($_GET['id'],$_GET['name']);
} elseif($sid = (int) $_GET['id']) {
    unset($_GET['id'],$_GET['name']);
    $_GET['sid'] = $sid;
} elseif($domain = _T($_GET['name'])) {
    //如果来路domain是act行为列表里的，则跳转到对应act页面
    if(in_array($domain, $acts)) location(url("item/$domain",'',1,1));
    unset($_GET['id'],$_GET['name']);
}

if(!$sid && !$domain) redirect(lang('global_sql_keyid_invalid', 'id'));
$id = $sid;

//取得主题信息
if($sid) {
    $detail = $I->read($sid);
} else {
    $_G['fullalways'] = TRUE;
    //域名合法性检测
    if(!$I->domain_check($domain)) redirect(lang('global_sql_keyid_invalid', 'id'));
    if($detail = $I->read($domain,'*',TRUE,TRUE)) {
        $_GET['sid'] = $sid = $detail['sid'];
    } else {
        location($_G['cfg']['siteurl']);
    }
}
if(!$detail||!$detail['status']) redirect('item_empty');

//主题管理员标记
$is_owner = $detail['owner'] == $user->username;


//added by whisk start
$detail['name'] = MaskIdNo($detail['name']);
//added by whisk end

$category = $I->get_category($detail['catid']);
if(!$pid = $category['catid']) {
    redirect('item_cat_empty');
}
$modelid = $I->category['modelid'];
$rogid = $I->category['review_opt_gid'];

//取得分类、模型、点评项以及地区信息
$catcfg =& $category['config'];
if(!$model = $I->get_model($modelid)) redirect('item_model_empty');
if($model['usearea']) $area = $I->loader->variable('area');

$R =& $_G['loader']->model(':review');
$reviewpot = $R->variable('opt_' . $rogid);

$urlpath = array();
$pcat = $I->variable('category');
$urlpath[] = url_path($I->category['name'], url("item/list/catid/$pid"));
$category = $_G['loader']->variable('category_'.$I->category['catid'], 'item');
if($category[$detail['catid']]['level'] > 2) {
    $urlpath[] = url_path($category[$category[$detail['catid']]['pid']]['name'], url("item/list/catid/{$detail['pid']}"));
}
$urlpath[] = url_path($category[$detail[catid]]['name'], url("item/list/catid/$detail[catid]"));

define('SUBJECT_CATID', $detail['pid']);
//生成表格内容
$detail_custom_field = $I->display_detailfield($detail);

//载入标签
$taggroups = $_G['loader']->variable('taggroup','item');
//增加浏览量
if(!$detail['owner'] || $detail['owner'] && $detail['owner'] != $user->username) $I->pageview($sid);
//加入COOKIE
$I->write_cookie($detail);

//显示点评或留言
$views = array('review','guestbook');
$view = $_GET['view'] = in_array($_GET['view'], $views) ? $_GET['view'] : 'review';

if($view == 'review') {
    if($detail['reviews'] > 0) {
        $review_filter = 'all';
        $review_orderby = 'posttime';
        //取得点评数据
        $where = array();
        $where['idtype'] = 'item_subject';
        $where['id'] = $sid;
        $where['status'] = 1;
        $orderby = array('posttime'=>'DESC');

        $offset = $MOD['review_num'] > 0 ? $MOD['review_num'] : 10;
        $start = get_start($_GET['page'], $offset);
        $select = 'r.*,m.point,m.coin,m.groupid';

        list(,$reviews) = $R->find($select, $where, $orderby, $start, $offset, FALSE, FALSE, TRUE);

        $onclick = "get_review('item_subject',$sid,'all','$review_orderby',{PAGE})";
        $multipage = multi($detail['reviews'], $offset, $_GET['page'], url("item/detail/id/$sid/view/review/page/_PAGE_"), '#review', $onclick);
    }
    //点评行为检测
    $review_access = $I->review_access($detail);
    $review_enable = $review_access['code'] == 1;

} elseif($view == 'guestbook' && $detail['guestbooks'] > 0) {
    //取得留言数据
    $GB =& $_G['loader']->model(MOD_FLAG.':guestbook');
    $where = array();
    $where['sid'] = $sid;
    $where['status'] = 1;
    $orderby = array('dateline'=>'DESC');
    $offset = $MOD['guestbook_num'] > 0 ? $MOD['guestbook_num'] : 10;
    $start = get_start($_GET['page'], $offset);
    list(,$guestbooks) = $GB->find($select, $where, $orderby, $start, $offset, FALSE);
    $onclick = "get_guestbook($sid,{PAGE})";
    $multipage = multi($detail['guestbooks'], $offset, $_GET['page'], SELF.'?sid='.$sid.'&view=guestbook','#guestbook',$onclick);
}

$_HEAD['keywords'] = $category['name'] . ',' . $pcat[$detail[catid]]['name'] . ',' . $catcfg['meta_keywords'];
$_HEAD['description'] = trimmed_title(strip_tags(str_replace("\r\n",'',$detail['content'])), 100) . ',' . $catcfg['meta_description'];

//分类设置默认风格
if($catcfg['templateid'] && !$detail['templateid']) {
    $detail['templateid'] = $catcfg['templateid'];
}
if($detail['templateid']) {
    include template('index', 'item', $detail['templateid']);
} else {
    //载入模型的内容页模板
    include template($I->model['tplname_detail']);
}