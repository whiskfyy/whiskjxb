<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$allow_ops = array( 'search', 'exists', 'myfavorite', 'myreviewed', 'get_membereffect', 'post_membereffect', 'post_map', 'post_log', 'clear_cookie_subject' );
$login_ops = array( 'post_membereffect', 'add_favorite' );

$op = empty($op) || !in_array($op, $allow_ops) ? '' : $op;

if(!$op) {
    redirect('global_op_unkown');
} elseif(!$user->isLogin && in_array($op, $login_ops)) {
    redirect('member_not_login');
}

switch($op) {

case 'search':

    $SEARCH =& $_G['loader']->model('item:search');
    $keyword = trim($_POST['keyword']);
    if($_G['charset'] != 'utf-8') {
        $_G['loader']->lib('chinese', NULL, FALSE);
        $CHS = new ms_chinese('utf-8', $_G['charset']);
        $keyword = $keyword ? $CHS->Convert($keyword) : '';
    }
    $_GET['keyword'] = $keyword;
    $_GET['catid'] = is_numeric($_GET['pid']) ? $_GET['pid'] : 0;
    $_GET['ordersort'] = 'addtime';
    $_GET['ordertype'] = 'desc';
    list($total, $list) = $SEARCH->search();
    if(!$total) {
        redirect('item_search_result_empty');
    } else {
        $category = $SEARCH->variable('category');
        echo "<ul>\n";
        while($val = $list->fetch_array()) {
            echo '<li sid="'.$val['sid'].'" catid="'.$val['catid'].'" name="'.$val['name'].$val['subname'].'">[&nbsp;'.$category[$val['pid']]['name'].'&nbsp;] '.$val['name'].'&nbsp;'.$val['subname']."</li>\n";
        }
        echo "</ul>";
        output();
    }
    break;

case 'exists':

	$city_id = _post('city_id','0','intval');
	$pid = _post('name','0','intval');
    $name = _post('name','','trim');
    if($_G['charset'] != 'utf-8') {
        $_G['loader']->lib('chinese', NULL, FALSE);
        $CHS = new ms_chinese('utf-8', $_G['charset']);
        $name = $name ? $CHS->Convert($name) : '';
    }
	if(!$name) redirect(lang('item_search_keyword_empty'));
	$S =& $_G['loader']->model('item:subject');
	$where = array();
	if($city_id) $where['city_id'] = $city_id;
	if($pid) $where['pid'] = $pid;
	$where['name'] = $name;
	list(,$list) = $S->find('sid,name,subname',$where,'sid',0,0);
	if(!$list) {
        redirect('item_search_result_empty');
    } else {
		while($val=$list->fetch_array()) {
			$content .= "\t<option value=\"$val[sid]\">$val[name]".($val['subname']?"($val[subname])":'')."</option>\r\n";
		}
		$list->free_result();
		echo $content;
		output();
	}
	break;

case 'myfavorite':

    $FAV =& $_G['loader']->model('item:favorite');
    $select = 'f.*,s.name,s.subname,s.pid,s.catid';
    $where = array();
    $where['f.uid'] = $user->uid;
    $start = get_start($_GET['page'], $offset = 50);
    list($total, $list) = $FAV->find($select,$where, $start, $offset);
    if(!$total) {
        redirect('item_search_result_empty');
    } else {
        $category = $FAV->variable('category');
        echo "<ul>\n";
        while($val = $list->fetch_array()) {
            echo '<li sid="'.$val['sid'].'" catid="'.$val['catid'].'" name="'.$val['name'].$val['subname'].'">[&nbsp;'.$category[$val['pid']]['name'].'&nbsp;] '.$val['name'].'&nbsp;'.$val['subname']."</li>\n";
        }
        echo "</ul>";
        output();
    }
    break;

case 'myreviewed':

    $R =& $_G['loader']->model(':review');
    $start = get_start($_GET['page'], $offset = 50);
    list($total, $list) = $R->myreviewed($user->uid, $start, $offset);
    if(!$total) {
        redirect('item_search_result_empty');
    } else {
        $category = $_G['loader']->variable('category','item');
        echo "<ul>\n";
        while($val = $list->fetch_array()) {
            echo '<li sid="'.$val['id'].'" catid="'.$val['pcatid'].'" name="'.$val['subject'].'">[&nbsp;'.$category[$val['pcatid']]['name'].'&nbsp;] '.$val['subject']."</li>\n";
        }
        echo "</ul>";
        output();
    }
    break;

case 'get_membereffect':

    if(!$sid = _post('sid', 0, 'intval')) redirect(lang('global_sql_keyid_invalid', 'sid'));
    if(!isset($_POST['effect'])) redirect(lang('member_effect_unkown_effect'));

    $S =& $_G['loader']->model('item:subject');
    if(!$subject = $S->read($sid,'pid,name,subname,pid,status',false)) redirect(lang('item_empty'));
    if(!$model = $S->get_model($subject['pid'], TRUE)) redirect('item_model_empty');

    $idtype = $model['tablename'];
    $effect = $_POST['effect'];

    $M =& $_G['loader']->model('member:membereffect');
    $M->add_idtype($idtype, 'subject', 'sid');

    if($_POST['member'] && $_POST['member'] != '0') {
        if($list = $M->get_member($sid, $idtype, $effect)) {
            while($val = $list->fetch_array()) {
                echo '<li><div><a title="'.$val['username'].'" href="'.url("space/index/uid/$val[uid]").'" target="_blank"><img src="'.get_face($val['uid']).'" />'.$val['username'].'</a></div></li>';
            }
        } else {
            redirect('global_empty_info');
        }
    } else {
        $totals = $M->total($sid, $idtype);
        if($totals) {
            foreach($totals as $key => $val) {
                if(substr($key, 0, 6) == 'effect') {
                    echo $split . $val;
                    $split = '|';
                }
            }
        } else {
            echo '0|0';
        }
    }
    output();
    break;

case 'post_membereffect':

    if(!$sid = _post('sid', 0, 'intval')) redirect(lang('global_sql_keyid_invalid', 'sid'));
    if(!isset($_POST['effect'])) redirect(lang('member_effect_unkown_effect'));

    $S =& $_G['loader']->model('item:subject');
    if(!$subject = $S->read($sid,'pid,name,subname,pid,status',false)) redirect(lang('item_empty'));
    if(!$model = $S->get_model($subject['pid'], TRUE)) redirect('item_model_empty');

    $idtype = $model['tablename'];
    $effect = $_POST['effect'];

    $M =& $_G['loader']->model('member:membereffect');
    $M->add_idtype($idtype, 'subject', 'sid');

    $M->save($sid, $idtype, $effect);

    if($totals = $M->total($sid, $idtype)) {
        foreach($totals as $key => $val) {
            if(substr($key, 0, 6) == 'effect') {
                echo $split . $val;
                $split = '|';
            }
        }
    } else {
        echo '0|0';
    }
    output();
    break;

case 'post_map':

    if(!$sid = (int)$_POST['sid']) redirect(lang('global_sql_keyid_invalid', 'sid'));
    $I =& $_G['loader']->model(MOD_FLAG.':subject');
    if(!$item = $I->read($sid)) {
        redirect(lang('item_empty'));
    }

    if($_POST['dosubmit']) {

        if($item['mappoint']) {
            $SL =& $_G['loader']->model(MOD_FLAG.':subjectlog');
            $_POST['ismappoint'] = 1;
            $_POST['upcontent'] = $_POST['p1'] . ',' . $_POST['p2'];
            if(!$user->isLogin) {
                $_POST['username'] = 'guest';
                $_POST['email'] = 'guest@guest.com';
            }
            $SL->save();
        } else {
            $I->mappoint($sid, $_POST['p1'].','.$_POST['p2']);
        }

        redirect('global_op_succeed');

    } else {

        include template('item_ajax_post_map');
        output();

    }

    break;

case 'post_log':

    if(!$sid = (int)$_POST['sid']) redirect(lang('global_sql_keyid_invalid', 'sid'));

    $I =& $_G['loader']->model(MOD_FLAG.':subject');
    if(!$item = $I->read($sid)) {
        redirect(lang('item_empty'));
    }

    if($_POST['dosubmit']) {

        $SL =& $_G['loader']->model(MOD_FLAG.':subjectlog');
        $_POST['ismappoint'] = 0;
        $SL->save();

        redirect('item_log_succeed');

    } else {

        include template('item_ajax_post_log');
        output();
    }

    break;

case 'clear_cookie_subject':

    del_cookie('subject_' . $_POST['pid']);
    break;

default:

    redirect('global_op_unkown');

}
?>