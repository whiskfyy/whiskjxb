<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
(!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied');

$ANN =& $_G['loader']->model('announcement');
$op = $_GET['op'] ? $_GET['op'] : $_POST['op'];

switch($op) {
case 'add':
    $_G['loader']->lib('editor',null,false);
    $editor = new ms_editor('content');
    $editor->item = 'admin';
    $edit_html = $editor->create_html();
    $admin->tplname = cptpl('announcement_save');
    break;
case 'edit':
    $id = (int)$_GET['id'];
    if(!$detail = $ANN->read($id)) return lang('global_ann_empty');
    $_G['loader']->lib('editor',null,false);
    $editor = new ms_editor('content');
    $editor->item = 'admin';
    $editor->content = $detail['content'];
    $edit_html = $editor->create_html();
    $admin->tplname = cptpl('announcement_save');
    break;
case 'save':
    $id = null;
    if($_POST['do']=='edit') {
        if(!$id = (int)$_POST['id']) redirect(lang('global_sql_keyid_invalid','id'));
    }
    $post = $ANN->get_post($_POST);
    $ANN->save($post,$id);
    redirect('global_op_succeed', cpurl($module,$act,'list'));
    break;
case 'update':
    $ANN->update($_POST['ann']);
    redirect('global_op_succeed', cpurl($module,$act,'list'));
    break;
case 'delete':
    $ANN->delete($_POST['ids']);
    redirect('global_op_succeed', get_forward(cpurl($module,$act)));
    break;
default:
    $op = 'list';
    $offset = 20;
    $start = get_start($_GET['page'],$offset);
    list($total,$list) = $ANN->find('id,title,orders,author,dateline,available',array(),'orders',$start,$offset,true);
    if($total) $multipage = multi($total, $offset, $_GET['page'], cpurl($module, $act, $op));
    $admin->tplname = cptpl('announcement_list');
}
?>