<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$op = $_POST['op'] ? $_POST['op'] : $_GET['op'];
$I =& $_G['loader']->model(MOD_FLAG.':subject');
switch($op) {
	default:
		$pid = isset($_GET['pid']) ? $_GET['pid'] : $MOD['pid'];
		(!$pid || !$I->get_category($pid)) and redirect('item_empty_default_pid');

		$category = $I->variable('category');
        if($category[$pid]['pid']) redirect('item_cat_invalid');

		$modelid = $category[$pid]['modelid'];
		$model = $I->variable('model_' . $modelid);
        $fields = $I->variable('field_' . $modelid);

        $varname = array('catid' => 'cattitle', 'name' => 'title', 'subname' => 'subtitle');
        foreach ($fields as $value) {
            if($var = $varname[$value['fieldname']]) {
                $$var = $value['title'];  
            }
        }

		$where['pid'] = (int) $pid;
		$where['creator'] = $user->username;
		$orderby = array('addtime', 'DESC');

		$select = 'sid,pid,catid,name,subname,status,reviews,pictures,guestbooks,addtime';

        $start = get_start($_GET['page'], $offset = 20);
        list($total, $list) = $I->find($select, $where, array('addtime'=>'DESC'), $start, $offset);
        $multipage = multi($total, $offset, $_GET['page'], url("item/member/ac/$ac/pid/$pid/page/_PAGE_"));

        $path_title = lang('item_title_m_subject');
		$tplname = 'subject_list';
}
?>