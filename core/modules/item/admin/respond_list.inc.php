<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
(!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied');

$R =& $_G['loader']->model(MOD_FLAG.':respond');
$op = isset($_POST['op']) ? $_POST['op'] : $_GET['op'];
$forward = get_forward(cpurl($module,$act));

switch ($op) {
     case 'delete':
        $R->delete($_POST['respondids'], TRUE, $_POST['delete_point']);
        redirect('global_op_succeed', $forward );
        break;
     default:
        $where = array();
        if($rid = $_GET['rid']) {
            $where['rp.rid'] = $rid;
        }
        $where['rp.status'] = 1;
        $select = 'rp.*,r.title,r.sid';
        $start = get_start($_GET['page'], $offset = 20);
        list($total, $list) = $R->find($select, $where, array('posttime'=>'DESC'), $start, $offset, TRUE, TRUE);
        $multipage = multi($total, $offset, $_GET['page'], cpurl($module, $act));

        $admin->tplname = cptpl('respond_list', MOD_FLAG);
 }