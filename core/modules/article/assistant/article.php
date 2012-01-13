<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
$A =& $_G['loader']->model(':article');
$op = $_POST['op'] ? $_POST['op'] : $_GET['op'];
$_G['loader']->helper('form',MOD_FLAG);
$_G['loader']->helper('misc',MOD_FLAG);
$_G['loader']->helper('form','item');
switch($op) {
case 'add':
    if($sid = _get('sid',null,MF_INT_KEY)) {
        $S =& $_G['loader']->model('item:subject');
        $subject = $S->read($sid);
        $mysubjects = $S->mysubject($user->uid);
        if(in_array($sid, $mysubjects)) $_GET['role'] = 'owner';
    }
    $_GET['role'] = $_GET['role'] == 'owner' ? 'owner' : 'member';
    if($_GET['role'] == 'member') $user->check_access('article_post', $A);
    $_G['loader']->lib('editor',null,false);
    $editor = new ms_editor('content');
    $editor->upimage = (int)$MOD['editor_image'];
    $edit_html = $editor->create_html();
    $tplname = 'article_save';
    break;
case 'edit':
    $articleid = _get('articleid',null,MF_INT_KEY);
    if(!$detail = $A->read($articleid)) redirect('article_empty');
    if($detail['sid']>0) {
        $mysubjects = $S->mysubject($user->uid);
        if(in_array($detail['sid'], $mysubjects)) $_GET['role'] = 'owner';
        $S =& $_G['loader']->model('item:subject');
        $subject = $S->read($detail['sid'],'*',false);
    }

    $_GET['role'] = _get('role','member',MF_TEXT);
    $_GET['role'] != 'owner' && $_GET['role'] = 'member';

    if($_GET['role'] == 'member') {
        if($detail['uid']!=$user->uid) redirect('global_op_access');
        $user->check_access('article_post', $A);
    }

    $_G['loader']->lib('editor',null,false);
    $editor = new ms_editor('content');
    $editor->upimage = (int)$MOD['editor_image'];
    $editor->content = $detail['content'];
    $edit_html = $editor->create_html();
    $tplname = 'article_save';
    break;
case 'save':
    if($_POST['do']=='edit') {
        $articleid = (int) $_POST['articleid'];
    } else {
        if($MOD['post_seccode']) check_seccode($_POST['seccode']);
        $articleid = null;
    }
    $post = $A->get_post($_POST);
    $articleid = $A->save($post,$articleid, $_POST['role']);
    $next_ac = $_POST['role'] == 'owner' ? 'g_article' : 'm_article';
    redirect(RETURN_EVENT_ID, get_forward(url('article/member/ac/'.$next_ac),1),array(lang('article_redirect_add')));
    break;
case 'delete':
    $A->delete($_POST['articleids'], TRUE); //同时删除积分
    redirect('global_op_succeed_delete', get_forward(url('article/member/ac/m_article')));
    break;
default:
    redirect('global_op_unkown');
}
?>