<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$_G['loader']->model('item:itembase', FALSE);
class msm_item_activity extends msm_item_itembase {

    var $table = 'dbpre_activity';
	var $key = 'aid';

	function __construct() {
		parent::__construct();
		$this->init_field();
	}

    function msm_item_activity() {
        $this->__construct();
    }

	function init_field() {
		$this->add_field('uid');
		$this->add_field_fun('uid', 'intval');
	}

	function save($uid,$username,$subjects,$reviews) {
        $time = date('Ymd', $this->global['timestamp']);
        if(!$uid || !$username) return;
        if(!$subjects && !$reviews) return;
        $this->db->from($this->table);
        $this->db->where('uid', $uid);
        $this->db->where('dateline', $time);
        $this->db->select('aid');
        $aid = $this->db->get_value();
        $this->db->sql_roll_back('from');
        if($aid) {
            $this->db->where('aid', $aid);
            $this->db->set_add('subjects', $subjects);
            $this->db->set_add('reviews', $reviews);
            $this->db->update();
        } else {
            $post = array(
                'dateline' => $time,
                'uid' => $uid,
                'username' => $username,
                'subjects' => $subjects,
                'reviews' => $reviews,
            );
            $this->db->set($post);
            $this->db->insert();
            $aid = $this->db->insert_id();
        }
        return $fid;
	}
}
?>