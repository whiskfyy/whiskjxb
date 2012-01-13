<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$op = $_POST['op'] ? $_POST['op'] : $_GET['op'];
$I =& $_G['loader']->model(MOD_FLAG.':subject');
$sid = $_GET['sid'] = (int) $_GET['sid'];

switch($op) {

	case 'delete':

		$pid = (int) $_GET['fw_pid'];
		$sid = (int) $_GET['sid'];
		if(!$detail = $I->read($sid, 'sid', FALSE)) redirect('global_op_empty');
		$I->delete($sid, TRUE);
		redirect('global_op_succeed', get_forward('home'));
		break;

	default:

		if($_POST['dosubmit']) {

			if(!$_POST['sid'] = (int) $_POST['sid']) {
				redirect(lang('global_sql_keyid_invalid', 'sid'));
			}
			$I->save($_POST['t_item'], $_POST['sid']);

            redirect('global_op_succeed', get_forward(url('item.detail/sid/'.$_POST['sid']), 1));

		} else {

			$pid = $_GET['pid'];
			if(!$detail = $I->read($sid, '*', TRUE)) redirect('global_op_empty');
            if(!$I->is_mysubject($sid, $user->uid)) {
                redirect('global_op_access');
            }
			$pid = $detail['pid'];
			define("ITEM_PID", $pid);
			define("EDIT_SID", $sid);
			$field_form = $I->create_form($pid, $detail, null);

			$tplname = 'subject_save';
		}
}
?>