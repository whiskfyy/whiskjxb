<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
define('SCRIPTNAV', 'item');

if(!$sid = (int) $_GET['sid']) {
    redirect(lang('global_sql_keyid_invalid', 'sid'));
}
$I =& $_G['loader']->model(MOD_FLAG.':subject');
if(!$subject = $I->read($sid, 'sid,pid,catid,name,subname')) {
    redirect('item_empty');
}
$P =& $_G['loader']->model(MOD_FLAG.':picture');

$op = _input('op');
switch($op) {
    case 'selectpic':
        $_G['in_ajax'] = 1;
        $where = array();
        $where['sid'] = (int) $_GET['sid'];
        $page = (int)$_GET['page'];
        $offset = 6;
        $start = get_start($page, $offset);
        list($total, $list) = $P->find('picid,thumb', $where, array('addtime'=>'DESC'), $start, $offset);
        if($total) {
            echo '<table width="100%">';
            $i = 0;
            while($value = $list->fetch_array()) {
                $i++;
                if($i % 3 == 1) echo '<tr>';
                echo '<td width="125"><img src="'.URLROOT.'/'.$value['thumb'].'" width="120" onclick="insert_subject_thumb(\''.$value['thumb'].'\');" style="cursor:pointer;" /></td>';
                if($i % 3 == 0) echo '</tr>';
            }
            if($x = $i % 3) {
                echo str_repeat('<td width="125">&nbsp;</td>', (3 - $x));
                echo '</tr>';
            }
            echo '</table><br /><center>';
            if($page > 1) echo '<a href="javascript:select_subject_thumb('.$where[sid].','.($page-1).');">&lt;&lt;</a>&nbsp;&nbsp;&nbsp;&nbsp;';
            if($start + $offset < $total) echo '<a href="javascript:select_subject_thumb('.$where[sid].','.($page+1).');">&gt;&gt;</a>';
            echo '</center>';
        }
        break;

    default:
        $catid = $subject['catid'];
        $I->get_category($catid);

        if(!$pid = $I->category['catid']) {
            redirect('item_cat_empty');
        }
        define('SCRIPTNAV', 'item_'.$pid);

        $modelid = $I->category['modelid'];
        $I->get_model($modelid);
        if(empty($I->model)) redirect('模块信息不存在。');

        if(!$_G['in_ajax']) {
            //取得主题信息
            if(!$detail = $I->read($sid)) {
                redirect('item_empty');
            }
            //取得分类和模型以及地区信息
            $category =& $I->category;
            $catcfg =& $I->category['config'];
            $model =& $I->model;

            $urlpath = array();
            $pcat = $I->variable('category');
            $urlpath[] = url_path($I->category['name'], url("item/list/catid/$pid"));
            $urlpath[] = url_path($pcat[$detail[catid]]['name'], url("item/list/catid/$detail[catid]"));

            $urlpath = array();
            $pcat = $I->variable('category');
            $urlpath[] = url_path($I->category['name'], url("item/list/catid/$pid"));
            $category = $_G['loader']->variable('category_'.$I->category['catid'], 'item');
            if($category[$detail['catid']]['level'] > 2) {
                $urlpath[] = url_path($category[$category[$detail['catid']]['pid']]['name'], url("item/list/catid/{$detail['pid']}"));
            }
            $urlpath[] = url_path($category[$detail[catid]]['name'], url("item/list/catid/$detail[catid]"));
            $urlpath[] = url_path($detail['name'].($detail['subname']?"({$detail['subname']})":''), url("item/detail/id/$detail[sid]"));
            $urlpath[] = url_path(lang('item_picture_title'), url("item/pic/sid/$detail[sid]"));
        }

        //取得图片列表
        $select = 'picid,uid,username,title,thumb,filename,addtime,comments';
        $where = array('sid' => (int)$sid);
        $orderby = array('addtime' => 'DESC');
        $start = get_start($_GET['page'], $offset = 1);
        list($total, $list) = $P->find($select, $where, $orderby, $start, 0, 1);
        if(!$total) {
            redirect('item_picture_empty');
        }

        $multipage = multi($total, $offset, $_GET['page'], "javascript:picture_page($sid,{PAGE});");

        $_HEAD['keywords'] = $catcfg['meta_keywords'];
        $_HEAD['description'] = $catcfg['meta_description'];
        include template('item_pic');
}