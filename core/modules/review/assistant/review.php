<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$op = _input('op');
$R =& $_G['loader']->model(MOD_FLAG.':review');
switch($op) {
	default:

        $idtype = _get('idtype','item_subject',MF_TEXT);

        $where['uid'] = $user->uid;
        $where['idtype'] = $idtype;
		$select = 'rid,id,posttime,status,flowers,responds,pcatid,idtype,id,title,subject';
        $start = get_start($_GET['page'], $offset = 20);
        list($total, $list) = $R->find($select, $where, array('posttime'=>'DESC'), $start, $offset, TRUE);
        //added by whisk start
        $resList = array();
        while($res = $list->fetch_array()){
            $subject = $res['subject'];
            $res['subject'] = MaskIdNo($subject);
            $resList[] = $res;
        }
        //added by whisk end
        $multipage = multi($total, $offset, $_GET['page'], url("review/member/ac/$ac/idtype/$idtype/page/_PAGE_"));

		$tplname = 'review_list';
}
?>