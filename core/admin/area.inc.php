<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
(!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied');

$op = _input('op');
$A =& $_G['loader']->model('area');

switch($op) {
case 'add':
    if(!$_POST['dosubmit']) {
        $pid = (int)$_GET['pid'];
        $admin->tplname = cptpl('area_save');
    } else {
        $A->save($_POST['area']);
        redirect('global_op_succeed', cpurl($module, $act, '', array('pid'=>$_POST['area']['pid'])));
    }
    break;
case 'edit':
    if(!$_POST['dosubmit']) {
        $aid = (int)$_GET['aid'];
        if(!$aid) redirect(sprintf(lang('global_sql_keyid_invalid'), 'aid'));
        if(!$detail = $A->read($aid)) {
            redirect('global_op_nothing');
        }
        $admin->tplname = cptpl('area_save');
    } else {
        $aid = (int)$_POST['aid'];
        if(!$aid) redirect(sprintf(lang('global_sql_keyid_invalid'), 'aid'));
        $A->save($_POST['area'], $aid);
        $forward = $_POST['forward'] ? _T($_POST['forward']) : cpurl($module, $act);
        redirect('global_op_succeed', $forward);
    }
    break;
case 'delete':
    $aid = $_GET['aid'];
    if(!$aid) redirect(sprintf(lang('global_sql_keyid_invalid'), 'aid'));
    $A->delete($aid);
    redirect('global_op_succeed', cpurl($module, $act));
    break;
default:
    if(!$_POST['dosubmit']) {
        if(!$pid = (int)$_GET['pid']) {
            $pid = 0;
            $level = 1;
        } else {
            if(!$detail = $A->read($pid)) {
                redirect('admincp_area_empty_pid');
            }
            $level = (int)$detail['level']+1;
        }
        $list = $A->get_list($pid);
    } else {
        $A->listorder($_POST['area']);
        redirect('global_op_succeed', get_forward(cpurl($module,$act)));
    }
    $admin->tplname = cptpl('area_list');
} 
?>