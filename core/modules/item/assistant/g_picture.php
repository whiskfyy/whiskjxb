<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$op = $_POST['op'] ? $_POST['op'] : $_GET['op'];
$P =& $_G['loader']->model(MOD_FLAG.':picture');
switch($op) {
	case 'delete':
		$P->delete($_POST['picids']);
		redirect('global_op_succeed_delete', get_forward(url('item/member/ac/'.$ac.'/pid/'.$_GET['fw_pid'])));
		break;
	case 'update':
		$P->update($_POST['picture']);
		redirect('global_op_succeed', get_forward(url('item/member/ac/'.$ac.'/pid/'.$_GET['fw_pid'])));
		break;
	default:
		$pid = isset($_GET['pid']) ? $_GET['pid'] : $MOD['pid'];
		(!$pid || !$P->get_category($pid)) and redirect('item_empty_default_pid');

		$category = $P->variable('category');
		$modelid = $category[$pid]['modelid'];
		$model = $P->variable('model_' . $modelid);
        $fields = $P->variable('field_' . $modelid);

        $varname = array('catid' => 'cattitle', 'name' => 'title', 'subname' => 'subtitle');
        foreach ($fields as $value) {
            if($var = $varname[$value['fieldname']]) {
                $$var = $value['title'];  
            }
        }

        $S =& $_G['loader']->model('item:subject');
        $mysubjects = $S->mysubject($user->uid);

        $where['pid'] = (int) $pid;
        $where['p.sid'] = $mysubjects;
        $select = 'p.*,s.name,s.subname,s.pid,s.catid';

        $start = get_start($_GET['page'], $offset = 10);
        list($total, $list) = $P->find($select, $where, array('addtime'=>'DESC'), $start, $offset);
        $multipage = multi($total, $offset, $_GET['page'], url("item/member/ac/g_picture/ac/$ac/pid/$pid/page/_PAGE_"));

        $path_title = lang('item_title_g_picture');
        $tplname = 'pic_list';
}
?>