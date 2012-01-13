<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
(!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied');

$A =& $_G['loader']->model(':article');
$op = $_GET['op'] ? $_GET['op'] : $_POST['op'];
$_G['loader']->helper('form',MOD_FLAG);
$_G['loader']->helper('misc',MOD_FLAG);

switch($op) {
case 'add':
    $admin->tplname = cptpl('article_save', MOD_FLAG);
    $_G['loader']->lib('editor',null,false);
    $editor = new ms_editor('content');
    $editor->item = 'admin';
    $edit_html = $editor->create_html();
    break;
case 'edit':
    if(!$detail = $A->read($_GET['articleid'])) redirect('article_empty');
    $_G['loader']->lib('editor',null,false);
    if($detail['sid']>0) {
        $S =& $_G['loader']->model('item:subject');
        $subject = $S->read($detail['sid'],'*',false);
    }
    $editor = new ms_editor('content');
    $editor->item = 'admin';
    $editor->width = '99%';
    $editor->content = $detail['content'];
    $edit_html = $editor->create_html();
    $admin->tplname = cptpl('article_save', MOD_FLAG);
    break;
case 'save':
    if($_POST['do'] == 'edit') {
        if(!$articleid = (int) $_POST['articleid']) redirect(lang('global_sql_keyid_invalid','articleid'));
    } else {
        $articleid = null;
    }
    $post = $A->get_post($_POST);
    $A->save($post, $articleid);
    $forward = $_POST['do'] == 'edit' ? get_forward(cpurl($module,$act,'list'),1) :cpurl($module,$act,'list');
    redirect('global_op_succeed', $forward);
    break;
case 'delete':
    $A->delete($_POST['articleids']);
    redirect('global_op_succeed', cpurl($module,$act,'list'));
    break;
case 'listorder':
    $A->listorder($_POST['articles']);
    redirect('global_op_succeed', get_forward(cpurl($module,$act,'list')));
    break;
case 'upatt':
    $A->upatt($_POST['articleids'],(int)$_POST['att_select']);
    redirect('global_op_succeed', get_forward(cpurl($module,$act,'list')));
    break;
case 'checkup':
    $A->checkup($_POST['articleids']);
    redirect('global_op_succeed', cpurl($module,$act,'checklist'));
case 'checklist':
    $where = array();
    $where['status'] = 0;
    $offset = 20;
    $start = get_start($_GET['page'],$offset);
    list($total,$list) = $A->find('articleid,subject,catid,att,dateline,uid,author,status',$where,'dateline',$start,$offset,true);
    if($total) {
        $multipage = multi($total, $offset, $_GET['page'], cpurl($module,$act,'checklist'));
    }
    $admin->tplname = cptpl('article_check', MOD_FLAG);
    break;
default:
    $op='list';
    if($_GET['dosubmit']) {
        $A->db->from($A->table);
        if($_GET['catid']) {
            $A->db->where('catid', $_GET['catid']);
        } elseif($_GET['pid']) {
            $C =& $_G['loader']->model('article:category');
            $cats = $C->get_sub_cats($_GET['pid']);
            $A->db->where_in('catid', array_keys($cats));
        }
        if($_GET['sid']) $A->db->where('sid', $_GET['sid']);
        if($_GET['subject']) $A->db->where_like('subject', '%'.$_GET['subject'].'%');
        if($_GET['author']) $A->db->where('author', $_GET['author']);
        if(is_numeric($_GET['att'])) $A->db->where('att', $_GET['att']);
        if($_GET['starttime']) $A->db->where_more('dateline', strtotime($_GET['starttime']));
        if($_GET['endtime']) $A->db->where_less('dateline', strtotime($_GET['endtime']));
        if($total = $A->db->count()) {
            $A->db->sql_roll_back('from,where');
            !$_GET['orderby'] && $_GET['orderby'] = 'cid';
            !$_GET['ordersc'] && $_GET['ordersc'] = 'ASC';
            $A->db->order_by($_GET['orderby'], $_GET['ordersc']);
            $A->db->limit(get_start($_GET['page'], $_GET['offset']), $_GET['offset']);
            $A->db->select('articleid,subject,catid,att,dateline,uid,author,comments,pageview,digg,status,listorder');
            $list = $A->db->get();
            /*
            $url = SELF;
            $split = '?';
            foreach($_GET as $k => $v) {
                if($k=='page') continue;
                $url .= $split . rawurlencode($k) .'=' . rawurlencode($v);
                $split  = '&';
            }
            */
            $multipage = multi($total, $_GET['offset'], $_GET['page'], cpurl($module,$act,'list',$_GET));
        }
    }
    $admin->tplname = cptpl('article_list', MOD_FLAG);
}
?>