<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

class msm_member_point extends ms_model {

    var $point = null;
    var $allow_del = true; //允许积分扣除到负数

    function __construct() {
        parent::__construct();
        $this->model_flag = 'member';
        $this->_load_point_rule();
    }

    function msm_member_point() {
        $this->__construct();
    }

    function update_point($uid, $sort, $delete=FALSE, $num = 1, $isusername = FALSE, $update = TRUE, $des='') {
        if($uid < 1 || !$uid) return FALSE;
        if(!$this->point) return FALSE;
        if(!isset($this->point[$sort])) return FALSE;
        $point = (int) $this->point[$sort]['point'];
        $coin = (int) $this->point[$sort]['coin'];
        if(!$point && !$coin && $update) return FALSE;
        if($delete && !$this->allow_del) {
            if($this->global['user']->uid == $uid) {
                if($point > 0 && $this->global['user']->point < $point) redirect(lang('member_point_less_point_self',$point));
                if($coin > 0 && $this->global['user']->coin < $coin) redirect(lang('member_point_less_coin_self',$coin));
            } else {
                $M =& $this->loader->model(':member');
                if(!$member = $M->read($uid)) return;
                if($point > 0 && $member['point'] < $point) redirect(lang('member_point_less_point',array($member['username'], $point)));
                if($coin > 0 && $member['coin'] < $coin) redirect(lang('member_point_less_coin',array($member['username'], $coin)));
            }
        }
        $fun = $delete ? 'set_dec' : 'set_add';
        if($point) $this->db->$fun('point', $point * $num);
        if($coin) $this->db->$fun('coin', $coin * $num);
        $this->db->where($isusername ? 'username' : 'uid', $uid);
        $this->db->from('dbpre_members');
        $update && $this->db->update();
        return TRUE;
    }

    //自由扣除，非固定值扣除
    function update_point2($uid,$sort,$point,$des='') {
        if(!$uid || !$point) return FALSE;
        $sorts = array('point','coin','rmb');
        if(!in_array($sort,$sorts)) return FALSE;
        $this->db->from('dbpre_members');
        if($point > 0) {
            $this->db->set_add($sort,$point);
        } else {
            $this->db->set_dec($sort, abs($point));
        }
        $this->db->where('uid', $uid);
        $this->db->update();
        return TRUE;
    }

    function _load_point_rule() {
        if($config = $this->variable('config')) {
            $this->point = $config['point'] ? unserialize($config['point']) : '';
        }
    }
}