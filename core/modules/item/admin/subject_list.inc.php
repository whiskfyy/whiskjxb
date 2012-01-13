<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
(!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied');

$I =& $_G['loader']->model(MOD_FLAG.':subject');
$op = isset($_POST['op']) ? $_POST['op'] : $_GET['op'];
$forward = get_forward(cpurl($module, $act,'',array('pid' => $_GET['pid'])));

switch ($op) {
    case 'delete':
        $I->delete($_POST['sids'], $_POST['delete_point']);
        redirect('global_op_succeed', $forward);
        break;
    case 'rebuild':
        $I->rebuild($_POST['sids']);
        redirect('global_op_succeed', $forward);
        break;
    case 'update':
        $I->update($_POST['subjects']);
        redirect('global_op_succeed', $forward);
        break;
    case 'move':
        $I->move($_POST['sids'], abs((int)$_POST['moveto_catid']));
        redirect('global_op_succeed', $forward);
        break;
    default:
        $pid = isset($_GET['pid']) ? $_GET['pid'] : $MOD['pid'];
		(!$pid || !$I->get_category($pid)) and redirect('item_empty_default_pid');

        $category = $I->variable('category');
		$modelid = $category[$pid]['modelid'];
		$model = $I->variable('model_' . $modelid);

        $I->db->from($I->table);
        $I->db->where('pid', $pid);
        if($_GET['catid']) $I->db->where('catid', $_GET['catid']);
        if($_GET['aid']) {
			$AREA =& $_G['loader']->model('area');
			$aids = $AREA->get_sub_aids($_GET['aid']);
			if($aids) $I->db->where('aid', $aids);
		}
        if($_GET['keyword']) $I->db->where_like('name', '%'.$_GET['keyword'].'%');
        $I->db->where('status', 1);
        $total = $I->db->count();
        if($total) {
            $I->db->sql_roll_back('from,where');
            $select = 'sid,aid,pid,catid,name,subname,finer,addtime,status,level,reviews,pictures,guestbooks,pageviews,thumb';
            if($model['usearea']) {
                $area = $_G['loader']->variable('area_1');
                $select .= ',aid';
            }
            $I->db->select($select);
            !$_GET['order'] && $_GET['order'] = 'addtime';
            !$_GET['by'] && $_GET['by'] = 'DESC';
            $I->db->order_by($_GET['order'], $_GET['by']);
            $start = get_start($_GET['page'], $offset = 20);
            $I->db->limit($start, $offset);
            $list = $I->db->get();
            $multipage = multi($total, $offset, $_GET['page'], cpurl($module, $act, 'list', $_GET));
        }

        $fields = $I->variable('field_' . $modelid);
        
        $varname = array('catid' => 'cattitle', 'name' => 'title', 'subname' => 'subtitle');
        foreach ($fields as $value) {
            if($var = $varname[$value['fieldname']]) {
                $$var = $value['title'];  
            }
        }
        
        $plist = array();
        foreach ($category as $key => $value) {
            if(!$value['pid']) {
                $plist[$value['catid']] = $value;
            }
        }
        
        unset($varname, $var, $key, $value);

		$_G['loader']->helper('form', $I->model_flag);
        
        $admin->tplname = cptpl('subject_list', MOD_FLAG);
}

function p_order($order) {
    if($_GET['order'] == $order) {
        if($_GET['by'] == 'ASC') return '↑';
        if($_GET['by'] == 'DESC') return '↓';
    }
}
?>