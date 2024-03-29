<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
(!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied');

$M =& $_G['loader']->model(':member');
$op = $_GET['op']?$_GET['op']:$_POST['op'];

$usergroup = $M->variable('usergroup');
$_G['loader']->helper('form', MOD_FLAG);

switch($op) {
case 'edit':
    if(!$_GET['uid'] = (int) $_GET['uid']) {
        redirect(lang('global_sql_keyid_invalid','uid'));
    } elseif(!$detail = $M->read($_GET['uid'])) {
        redirect('global_op_empty');
    }
    $UP =& $_G['loader']->model('member:usergroup');
    $admin->tplname = cptpl('member_save', MOD_FLAG);
    break;
case 'post':
    $member = $M->get_post($_POST['member']);
    if($_POST['uid'] = (int) $_POST['uid']) {
        $member['password2'] = $_POST['member']['password2'];
    }
    $M->save($member, $_POST['uid']);
    redirect('global_op_succeed', get_forward(cpurl($module,$act,'list'),1));
    break;
case 'delete':
    if(!$_POST['uids'] || !is_array($_POST['uids'])) redirect('global_op_unselect');
    $M->delete($_POST['uids']);
    redirect('global_op_succeed', get_forward(cpurl($module,$act,'list')));
    break;
default:
    $op = 'list';
    if($_GET['dosubmit']) {
        $M->db->from($M->table);
        if($_GET['groupid']) $M->db->where('groupid', $_GET['groupid']);
        if($_GET['username']) $M->db->where('username', $_GET['username']);
        if($_GET['zreg_starttime']) $M->db->where_more('regdate', strtotime($_GET['zreg_starttime']));
        if($_GET['zreg_endtime']) $M->db->where_less('regdate', strtotime($_GET['zreg_endtime']));
        if($_GET['login_starttime']) $M->db->where_more('logintime', strtotime($_GET['login_starttime']));
        if($_GET['login_endtime']) $M->db->where_less('logintime', strtotime($_GET['login_endtime']));
        if($_GET['email']) $M->db->where('email', $_GET['email']);
        if($_GET['loginip']) $M->db->where('loginip', $_GET['loginip']);
        if($total = $M->db->count()) {
            $M->db->sql_roll_back('from,where');
            !$_GET['orderby'] && $_GET['orderby'] = 'uid';
            !$_GET['ordersc'] && $_GET['ordersc'] = 'ASC';
            $M->db->order_by($_GET['orderby'], $_GET['ordersc']);
            $M->db->limit(get_start($_GET['page'], $_GET['offset']), $_GET['offset']);
            $list = $M->db->get();
            $multipage = multi($total, $_GET['offset'], $_GET['page'], cpurl($module,$act,'list',$_GET));
        }
    }
    $admin->tplname = cptpl('member_list', MOD_FLAG);
}
?>