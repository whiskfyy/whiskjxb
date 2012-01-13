<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

class msm_exchange extends ms_model {

    var $table = 'dbpre_exchange_log';
	var $key = 'exchangeid';
    var $model_flag = 'exchange';

	function __construct() {
		parent::__construct();
        $this->model_flag = 'exchange';
        $this->modcfg = $this->variable('config');
        $this->init_field();
	}

    function msm_exchange() {
        $this->__construct();
    }

	function init_field() {
        $this->add_field('giftid,number,linkman,contact,postcode,address,des');
        $this->add_field_fun('giftid,number', 'intval');
        $this->add_field_fun('linkman,contact,postcode,address,des', '_T');
    }

    function save($post) {
        $gift = $this->check_exchange((int)$post['giftid']);
        $post['giftname'] = $gift['name'];
        $post['price'] = $gift['price'];
        $post['uid'] = $this->global['user']->uid;
        $post['username'] = $this->global['user']->username;
        $post['exchangetime'] = $this->global['timestamp'];
        $post['status'] = 1;
        //兑换总量
	    $total_price = $gift['price'] * (int) $post['number'];
	    if($this->global['user']->coin < $total_price) {
            redirect('exchange_check_price_less');
        }
        if($gift['num'] < $post['number']) redirect('exchange_check_stockout2');
        $this->check_post($post,false);
        $out_arr = array('linkman','contact','postcode','address','des');
        $params = array();
        foreach($out_arr as $k) {
            $params[] = $post[$k];
            unset($post[$k]);
        }
        $post['contact'] = lang('exchange_contact_format',$params);
        //提交
        $exchange = parent::save($post,null,false,false,false);
        //会员积分变化
        $this->member_coin($this->global['user']->uid, -$total_price);
        //较少库存
        $GT =& $this->loader->model($this->model_flag.':gift');
        $GT->salevolume($gift['giftid'], $post['number']);
        return $exchangeid;
    }

    //更新状态
    function update($exchangeid,$status,$des='') {
        if(!$exchangeid) return;
        if($status == '4') $this->refund($exchangeid); //退款
        $this->db->from($this->table);
        $this->db->set('status',$status);
        $this->db->set('status_extra',$des);
        $this->db->set('checker',$this->global['admin']->adminname);
        $this->db->where('exchangeid',$exchangeid);
        $this->db->update();
    }

    //退款
    function refund($exchangeid) {
        if(!$exchangeid) return;
        if(!$detail = $this->read($exchangeid)) return;
        //会员积分返还
        $price = $detail['price'] * $detail['number'];
        $this->member_coin($detail['uid'], $price);
        //累计库存
        $GT =& $this->loader->model($this->model_flag.':gift');
        $GT->salevolume($detail['giftid'], -$detail['number']);
    }

    //删除
    function delete($exids, $return_point = false,$return_gift = false) {
        if(is_numeric($exids) && $exids > 0) $exids = array($exids);
        if(!is_array($exids) || !$exids) redirect('global_op_unselect');
        if(!$return_point && !$return_gift) {
            parent::delete($exids);
            return;
        }
        $this->db->from($this->table);
        $this->db->where_in('exchangeid', $exids);
        $this->db->select('exchangeid,uid,number,price,status,giftid');
        if(!$q=$this->db->get()) return;
        $dels = array();
        if($return_gift) $GT =& $this->loader->model($this->model_flag.':gift');
        while($v=$q->fetch_array()) {
            $dels[] = $v['exchangeid'];
            //会员积分返还
            if($return_point) $this->member_coin($v['uid'], $v['price'] * $v['number']);
            //还原库存可销售
            if($return_gift) $GT->salevolume($v['giftid'], -$v['number']);
        }
        parent::delete($dels);
    }

    function check_post(& $post, $edit = false) {
        if(!$post['linkman']) {
            redirect('exchange_post_linkman_empty');
        } elseif(!is_numeric($post['number']) || $post['number'] < 1) {
            redirect('exchange_post_number_less');
        } elseif(!$post['contact']) {
            redirect('exchange_post_contact_empty');
        } elseif(!preg_match('/^[0-9]+$/', $post['postcode'])) {
            redirect('exchange_post_postcode_format_error');
        } elseif(!$post['address']) {
            redirect('exchange_post_address_empty');
        }
    }

    function check_exchange($giftid) {
        $GT =& $this->loader->model($this->model_flag.':gift');
        if(!$gift = $GT->read($giftid)) redirect('exchange_gift_empty');
        if(!$gift['available']) redirect('exchange_check_invalid');
        if($gift['num'] <= 0) redirect('exchange_check_stockout');
        if($this->global[user]->coin < $gift['price']) redirect('exchange_check_price_less');
        return $gift;
    }

    function check_access($key,$value,$jump) {
        if($this->in_admin) return TRUE;
        if($key == 'exchange_disable') {
            $value = (int) $value;
            if($value) {
                if(!$jump) return FALSE;
                redirect('exchange_access_disable');
            }
        } elseif($key == 'exchange_disable_price') {
            $value = (int) $value;
            if($this->gift_price > $value) {
                if(!$jump) return FALSE;
                redirect(lang('exchange_access_disable_price',$value));
            }
        }
        return TRUE;
    }

    function status_total($uid=null) {
        $this->db->from($this->table);
        $this->db->select('status');
        $this->db->select('*', 'count', 'COUNT( ? )');
        $uid && $this->db->where('uid',$uid);
        $this->db->group_by('status');
        if(!$q = $this->db->get())return array();
        $result = array();
        while($v=$q->fetch_array()) {
            $result[$v['status']] = $v['count'];
        }
        $q->free_result();
        return $result;
    }

    //会员积分变化
    function member_coin($uid,$point) {
        $P =& $this->loader->model('member:point');
        $P->update_point2($uid, 'coin', $point);
    }
}
?>