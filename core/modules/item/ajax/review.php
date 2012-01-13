<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$allow_ops = array( 'get', 'add_flower', 'get_flower', 'post_report' );
$login_ops = array( 'add_flower' );

$op = empty($op) || !in_array($op, $allow_ops) ? '' : $op;

if(!$op) {
    redirect('global_op_unkown');
} elseif(!$user->isLogin && in_array($op, $login_ops)) {
    redirect('member_not_login');
}

switch($op) {

case 'get':

    if(!$sid = (int)$_POST['sid']) redirect(lang('global_sql_keyid_invalid', 'sid'));
    //取得点评数据
    $S =& $_G['loader']->model(MOD_FLAG.':subject');
    if(!$subject = $S->read($sid,'sid,pid,catid,name,subname,status',FALSE)) {
        redirect('item_empty');
    }

    $S->get_category($subject['pid']);
    if(!$modelid = $S->category['modelid']) {
        redirect(lang('global_sql_keyid_invalid', 'modelid'));
    }
	$rogid = $S->category['review_opt_gid'];
    $catcfg =& $S->category['config'];

    $order_list = array(
        'posttime' => array('posttime'=>'DESC'), 
        'flower' => array('flowers'=>'DESC'), 
        'respond' => array('responds'=>'DESC'), 
    );

    $review_order = $order_list[$_POST['order']] ? $_POST['order'] : 'posttime';

    //取得点评数据
    $R =& $_G['loader']->model(MOD_FLAG.':review');
    $where = array();
    $where['sid'] = $sid;
    $where['status'] = 1;
    $orderby = $order_list[$review_order];
    $offset = $MOD['review_num'] > 0 ? $MOD['review_num'] : 10;
    $start = get_start($_GET['page'], $offset);
    $select = 'r.*,m.point,m.coin,m.groupid';

    list($total, $reviews) = $R->find($select, $where, $orderby, $start, $offset, TRUE, FALSE, TRUE);
    if($total) {
        $onclick = "get_review($sid,'".$_POST['order']."',{PAGE})";
        $multipage = multi($total, $offset, $_GET['page'], url("item/detail/id/$sid/view/review/page/_PAGE_"), '#review', $onclick);
    }

    //点评项
    $reviewpot = $R->variable('review_opt_' . $rogid);
    //标签组
    $taggroups = $R->variable('taggroup');

    include template('item_subject_detail_review');
    break;

case 'post':

    if($_POST['dosubmit']) {

    } else {
        $_G['loader']->helper('form');

        $sid = (int)$_GET['sid'];

        $goto = 'item/member/ac/review_edit/rid/{rid}';
        $subject = $R->check_review_before($sid, FALSE, $goto);
        unset($goto);

        $pid = $subject['pid'];
        $category = $R->variable('category');
        $modelid = $category[$pid]['modelid'];
        $rogid = $category[$pid]['review_opt_gid'];
        $catcfg = $category[$pid]['config'];
        $model = $R->variable('model_' . $modelid);
        $review_opts = $R->variable('review_opt_' . $rogid);
        $taggroups = $R->variable('taggroup');
    }
    break;

case 'add_flower':

    if(!$rid = (int)$_POST['rid']) redirect(lang('global_sql_keyid_invalid', 'rid'));

    $R =& $_G['loader']->model(MOD_FLAG.':review');
    if(!$review = $R->read($rid)) {
        redirect(lang('item_review_empty'));
    } elseif($review['uid'] == $user->uid) {
        redirect(lang('item_flower_myself'));
    }

    $F =& $_G['loader']->model(MOD_FLAG.':flower');
    $post = array('rid'=>$rid);
    $F->save($post);

    break;

case 'get_flower':

    if(!$rid = (int)$_POST['rid']) redirect(lang('global_sql_keyid_invalid', 'rid'));

    $R =& $_G['loader']->model(MOD_FLAG.':review');
    if(!$review = $R->read($rid)) {
        redirect(lang('item_review_empty'));
    }

    $F =& $_G['loader']->model(MOD_FLAG.':flower');
    $where = array ( 'rid' => $rid );
    $offset = 100;
    $start = get_start($_GET['page'], $offset);
    list($total, $list) = $F->find($where, $start, $offset);
    if(!$total) redirect('global_empty_info');
    if($total) {
        $multipage = multi($total, $offset, $_GET['page'], 'javascript:get_flower('.$rid.', {PAGE});');
    }
    include template('item_ajax_get_flower');
    output();

    break;

case 'post_report':

    if(!$rid = (int)$_POST['rid']) redirect(lang('global_sql_keyid_invalid', 'rid'));
    if($_POST['dosubmit']) {

        $R =& $_G['loader']->model(MOD_FLAG.':report');
        $R->get_category($pid);
        $reportid = $R->save();
        redirect('item_report_succeed');

    } else {

        $R =& $_G['loader']->model(MOD_FLAG.':review');

        if(!$review = $R->read($rid)) {
            redirect(lang('item_review_empty'));
        }

        include template('item_ajax_post_report');
        output();
    }

    break;

default:

    redirect('global_op_unkown');

}
?>