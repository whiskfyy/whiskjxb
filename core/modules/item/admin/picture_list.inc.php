<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
(!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied');

$P =& $_G['loader']->model(MOD_FLAG.':picture');
$op = isset($_POST['op']) ? $_POST['op'] : $_GET['op'];

switch ($op) {
case 'upload':
    $P->save($P->get_post($_POST));
    redirect('global_op_succeed', cpurl($module, $act));
    break;
case 'delete':
    $P->delete($_POST['picids']);
    redirect('global_op_succeed', cpurl($module, $act,'',array('pid' => $_GET['pid'])));
    break;
case 'update':
    $P->update($_POST['picture']);
    redirect('global_op_succeed', cpurl($module, $act,'',array('pid' => $_GET['pid'])));
    break;
default:
    $pid = isset($_GET['pid']) ? $_GET['pid'] : $MOD['pid'];
    (!$pid || !$P->get_category($pid)) and redirect('item_empty_default_pid');
    $category = $P->variable('category');
    $modelid = $category[$pid]['modelid'];
    $model = $P->variable('model_' . $modelid);

    $where = array();
    if($sid = (int)$_GET['sid']) {
        $where['p.sid'] = $sid;
    }
    $where['pid'] = (int)$pid;
    $where['p.status'] = 1;
    $select = 'p.*,pid,catid,name,subname';
    $start = get_start($_GET['page'], $offset = 20);
    list($total, $list) = $P->find($select, $where, array('addtime'=>'DESC'), $start, $offset);
    $multipage = multi($total, $offset, $_GET['page'], cpurl($module, $act, '', array('pid' => $pid)));

    $admin->tplname = cptpl('picture_list', MOD_FLAG);
 }