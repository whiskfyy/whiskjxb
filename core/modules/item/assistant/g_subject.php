<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$op = $_POST['op'] ? $_POST['op'] : $_GET['op'];
$S =& $_G['loader']->model(MOD_FLAG.':subject');
switch($op) {
	case 'mysubject':
		$_G['loader']->helper('form','item');
		$content = form_item_mysubject($user->uid);
		echo $content;
		exit;
		break;
	default:
		$pid = _get('pid',$MOD['pid'],'intval');
		(!$pid || !$S->get_category($pid)) and redirect('item_empty_default_pid');
		
		$category = $S->variable('category');
		$modelid = $category[$pid]['modelid'];
		$model = $S->variable('model_' . $modelid);
        $fields = $S->variable('field_' . $modelid);
        
        $varname = array('catid' => 'cattitle', 'name' => 'title', 'subname' => 'subtitle');
        foreach ($fields as $value) {
            if($var = $varname[$value['fieldname']]) {
                $$var = $value['title'];  
            }
        }

        $S->db->join('dbpre_mysubject', 'ms.sid', 'dbpre_subject', 's.sid');
        $S->db->where('uid', $user->uid);
        $S->db->where('pid', $pid);
        if($total = $S->db->count()) {
            $start = get_start($_GET['page'], $offset = 20);
            $S->db->sql_roll_back('from,where');
            $S->db->select('ms.*,aid,pid,catid,name,subname,status,reviews,pictures,guestbooks,addtime');
		    $S->db->order_by('addtime','DESC');
            $list = $S->db->get();
        $multipage = multi($total, $offset, $_GET['page'], url("item/member/ac/$ac/pid/$pid/page/_PAGE_"));
        }

        $path_title = lang('item_title_g_subject');
		$tplname = 'subject_list';
}
?>