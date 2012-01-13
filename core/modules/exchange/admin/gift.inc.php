<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
(!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied');

$GT =& $_G['loader']->model(MOD_FLAG.':gift');
$op = $_GET['op'] ? $_GET['op'] : $_POST['op'];
$_G['loader']->helper('form','member');

switch($op) {
case 'add':
    $admin->tplname = cptpl('gift_save', MOD_FLAG);
    $_G['loader']->lib('editor',null,false);
    $editor = new ms_editor('description');
    $editor->item = 'admin';
    $edit_html = $editor->create_html();
    break;
case 'edit':
    if(!$detail = $GT->read($_GET['giftid'])) redirect('exchange_gift_empty');
    $_G['loader']->lib('editor',null,false);
    $editor = new ms_editor('description');
    $editor->item = 'admin';
    $editor->content = $detail['description'];
    $edit_html = $editor->create_html();
    $admin->tplname = cptpl('gift_save', MOD_FLAG);
    break;
case 'save':
    if($_POST['do'] == 'edit') {
        if(!$giftid = (int) $_POST['giftid']) redirect(lang('global_sql_keyid_invalid','giftid'));
    } else {
        $giftid = null;
    }
    $post = $GT->get_post($_POST);
    $GT->save($post, $giftid);
    redirect('global_op_succeed', get_forward(cpurl($module,$act,'list'),1));
    break;
case 'delete':
    $GT->delete($_POST['giftids']);
    redirect('global_op_succeed', cpurl($module,$act,'list'));
    break;
case 'update':
    $GT->update($_POST['gifts']);
    redirect('global_op_succeed', cpurl($module,$act,'list'));
    break;
default:
    $op='list';
    $offset = 20;
    $start = get_start($_GET['page'],$offset);
    list($total,$list) = $GT->find('giftid,name,available,displayorder,price,num,salevolume',null,'displayorder',$start,$offset,true);
    if($total) {
        $multipage = multi($total, $offset, $_GET['page'], cpurl($module,$act,'list'));
    }
    $admin->tplname = cptpl('gift_list', MOD_FLAG);
}
?>