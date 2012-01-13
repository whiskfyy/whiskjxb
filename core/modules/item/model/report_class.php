<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$_G['loader']->model('item:itembase', FALSE);
class msm_item_report extends msm_item_itembase {

    var $table = 'dbpre_reports';
	var $key = 'reportid';

	function __construct() {
		parent::__construct();
		$this->init_field();
	}

    function msm_item_report() {
        $this->__construct();
    }

	function init_field() {
		$this->add_field('rid,username,email,sort,reportcontent');
		$this->add_field_fun('rid,sort', 'intval');
        $this->add_field_fun('username,email', '_T');
        $this->add_field_fun('reportcontent', '_TA');
	}

	function find($select, $where, $start, $offset, $total = TRUE) {
	    $this->db->join($this->table,'rt.rid',$this->review_table,'r.rid','LEFT JOIN');
		$this->db->where($where);

        $result = array(0,'');
        if($total) {
            if(!$result[0] = $this->db->count()) {
                return $result;
            }
            $this->db->sql_roll_back('from,where');
        }
        
		$this->db->select($select ? $select : '*');
        $this->db->order_by('rt.posttime', 'DESC');
        $this->db->limit($start, $offset);
        $result[1] = $this->db->get();
        return $result;
	}

	function save() {
        $post = $this->get_post($_POST);
        if($this->global['user']->isLogin) {
            $post['uid'] = $this->global['user']->uid;
            $post['username'] = $this->global['user']->username;
            $post['email'] = $this->global['user']->email;
        }
        $post['posttime'] = $this->global['timestamp'];
        $post['disposal'] = 0;
        $post['reportremark'] = '';

		$fid = parent::save($post, null, FALSE);

        return $fid;
	}

	function check_post(& $post, $isedit = FALSE) {
        if($isedit && !is_numeric($post['rid'])) {
            redirect(lang('global_sql_keyid_invalid', 'rid'));
        } elseif(!is_numeric($post['sort'])) {
            redirect('item_report_empty_sort');
        } elseif(!$post['reportcontent']) {
            redirect('item_report_empty_content');
        }
        if(!$is_edit) {
            $R =& $this->loader->model(MOD_FLAG.':review');
            if(!$review = $R->read($post['rid'])) {
                redirect(lang('item_review_empty'));
            }
        }
	}

    function disposal($post, $reportid) {
        if(!$detail = $this->read($reportid,'rid,uid,username,posttime')) {
            redirect('item_report_empty');
        }
        $data = array(
            'disposal' => 1,
            'disposaltime' => $this->global['timestamp'], 
            'reportremark' => _T($post['reportremark'])
        );
        if($post['update_point'] && $detail['uid'] > 0 && !$detail['update_point']) {
            $data['update_point'] = (int) $post['update_point'];
        }

        $this->db->from($this->table);
        $this->db->set($data);
        $this->db->where('reportid', $reportid);
        $this->db->update();

        if($post['delete']) {
            $R =& $this->loader->model($this->model_flag.':review');
            $R->delete($detail['rid']);
        }
        if($data['update_point'] && $detail['uid'] > 0) {
            $P =& $this->loader->model('member:point');
            $P->update_point($detail['uid'], 'report_review', 0);
        }
    }

	function delete($reportids) {
		if(empty($reportids)) redirect('global_op_unselect');
		if(!is_array($reportids)) $fids = array((int)$reportids);
		$this->db->from($this->table);
		$this->db->where_in('reportid', $reportids);
		$this->db->delete();
	}
}
?>