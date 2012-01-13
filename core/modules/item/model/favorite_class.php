<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$_G['loader']->model('item:itembase', FALSE);
class msm_item_favorite extends msm_item_itembase {

    var $table = 'dbpre_favorites';
	var $key = 'fid';

	function __construct() {
		parent::__construct();
		$this->init_field();
	}

    function msm_item_favorite() {
        $this->__construct();
    }

	function init_field() {
		$this->add_field('sid');
		$this->add_field_fun('sid', 'intval');
	}

	function find($select, $where, $start, $offset, $total = TRUE) {
	    $this->db->join($this->table,'f.sid',$this->subject_table,'s.sid');
		$this->db->where($where);

        $result = array(0,'');
        if($total) {
            if(!$result[0] = $this->db->count()) {
                return $result;
            }
            $this->db->sql_roll_back('from,where');
        }
        
        $this->db->select($select?$select:'f.fid,f.sid,f.uid,f.username,f.addtime,s.name,s.subname,s.pid,s.catid');
        $this->db->order_by('f.addtime', 'DESC');
        $this->db->limit($start, $offset);
        $result[1] = $this->db->get();
        return $result;
	}

	function save($post) {
        $this->check_post($post);
        if($this->submitted($this->global['user']->uid, $post['sid'])) {
            redirect('item_favorite_submitted');
        }

        $post['uid'] = $this->global['user']->uid;
        $post['username'] = $this->global['user']->username;
        $post['addtime'] = $this->global['timestamp'];

		$fid = parent::save($post, null, FALSE, FALSE, FALSE);
        $this->subject_total($post['sid']);

        return $fid;
	}

    function submitted($uid, $sid) {
        $this->db->from($this->table);
        $this->db->where('uid', $uid);
        $this->db->where('sid', $sid);
        return $this->db->count() >= 1;
    }

	function check_post(& $post, $isedit = FALSE) {
        if($isedit && !is_numeric($post['sid'])) {
            redirect(lang('global_sql_keyid_invalid', 'sid'));
        }
	}

	function delete($fids, $update_total = TRUE) {
		$fids = parent::get_keyids($fids);
		$where = array('fid'=>$fids);
		$this->_delete($where, $update_total);
	}

    function delete_sids($sids, $update_total = FALSE) {
		$sids = parent::get_keyids($sids);
		$where = array('sid'=>$sids);
		$this->_delete($where, $update_total);
    }

	function subject_total($sid, $num=1) {
		if(!$num) return;
		$fun = $num > 0 ? 'set_add' : 'set_dec';
		$num = abs($num);
		$this->db->from('dbpre_subject');
		$this->db->where('sid', $sid);
		$this->db->$fun('favorites',$num);
		$this->db->update();
	}

	function _delete($where, $update_total=TRUE) {
		$this->db->from($this->table);
		$this->db->where($where);
		if(!$q = $this->db->get()) return ;
		$delids = $sids = array();
		while($v=$q->fetch_array()) {
			$delids[] = $v['fid'];
			if(!$update_total) continue;
			if(!isset($sids[$v['sid']])) {
				$sids[$v['sid']]=0;
			}
			$sids[$v['sid']]++;
		}
		$q->free_result();
		if($update_total && $sids) {
			foreach($sids as $sid=>$num) {
				$this->subject_total($sid,-$num);
			}
		}
		$this->db->from($this->table);
		$this->db->where('fid', $delids);
		$this->db->delete();
	}
}
?>