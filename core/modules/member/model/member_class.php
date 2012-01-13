<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

class msm_member extends ms_model {

    var $table = 'dbpre_members';
    var $key = 'uid';

    var $modcfg;

    function __construct() {
        parent::__construct();
        $this->model_flag = 'member';
        $this->modcfg = $this->loader->variable('config','member');
        $this->init_field();
    }

    function msm_member() {
        $this->__construct();
    }

    function init_field() {
		$this->add_field('uid,username,password,password2,email,groupid,nexttime,nextgroupid,point,coin');
		$this->add_field_fun('username', '_T');
        $this->add_field_fun('uid,groupid,nexttime,nextgroupid', 'intval');
        $this->add_field_fun('password,email', 'trim');
    }

    function & read($uid,$isusername=FALSE,$select='*') {
        $result = '';
        if(!$isusername) {
            if(!$uid = (int) $uid) return $result;
            $result = parent::read($uid);
            return $result;
        }
        $this->db->from($this->table);
        $this->db->select($select);
        $this->db->where('username', $uid);
        if($row = $this->db->get()) {
            $result = $row->fetch_array();
            $row->free_result();
        }
        return $result;
    }

    function & find($where, $start, $offset) {
        $result = array();
        $this->db->from($this->table);
        if($where['username']) {
            $this->db->where_like('username', '%'.$where['username'].'%');
            unset($where['username']);
        }
        if($where) {
            foreach($where as $key => $val) $this->db->where($key, $val);
        }
        $total = $this->db->count();
        if(!$total) {
            $result = array(0,'');
            return $result;
        }
        $this->db->sql_roll_back('from,where');
        $this->db->order_by('uid');
        $this->db->limit($start, $offset);
        $result = array($total, $this->db->get());
        return $result;
    }

    function save(& $post,$uid=null) {
        $edit = $uid > 0;
        if($post['password'] != $post['password2']) {
            redirect('member_post_empty_eq_pw');
        }
        if($this->in_admin && empty($post['password'])) unset($post['password']);
        if($post['password']) $post['password'] = md5($post['password']);
        unset($post['password2']);
        $post['nexttime'] = $post['nexttime'] ? strtotime($post['nexttime']) : 0;
        $uid = parent::save($post, $uid);

        // update point
        if(!$edit) {
            $P =& $this->loader->model('member:point');
            $P->update_point($uid, 'reg');
            //发送短消息
            if($this->modcfg['salutatory']) $this->_send_salutatory_msg($uid);
        }
        return $uid;
    }

    //从反向整合
    function save_passport($post) {
        $this->db->from($this->table);
        $this->db->set($post);
        $this->db->insert();
        $uid = $this->db->insert_id();
        if(!$edit) {
            $P =& $this->loader->model('member:point');
            $P->update_point($uid, 'reg');
            //发送短消息
            if($this->modcfg['salutatory']) $this->_send_salutatory_msg($uid);
        }
        return $uid;
    }

    //更新积分
    function update_point($uid,$point) {
        if(!$uid = abs ( (int) $uid)) redirect(lang('global_sql_keyid_invalid','uid'));
        $this->db->from($this->table);
        $this->db->where('uid',$uid);
        $this->db->set($point);
        $this->db->update();
    }

    function delete($ids) {
        $ids = $this->get_keyids($ids);
        $this->db->where_in($this->key, $ids);
        $this->db->from($this->table);
        $this->db->delete();
        //其他模块关联的HOOK
        foreach(array_keys($this->global['modules']) as $flag) {
            if($flag == $this->model_flag) continue;
            $file = MUDDER_MODULE . $flag . DS . 'inc' . DS . 'member_delete_hook.php';
            if(is_file($file)) {
                @include $file;
            }
        }
    }

    function check_post(& $post, $isedit) {
        $this->loader->helper('validate');
        if(!$post['username'] && !$isedit) {
            redirect('member_post_empty_name');
        } elseif(!$isedit && $post['username']) {
            $this->check_username($post['username']);
        }
        if(!$post['password'] && !$isedit) {
            redirect('member_post_empty_pw');
        }
        if($post['password']) {
            $this->check_password($post['password']);
        }
        $this->loader->helper('validate');
        if(!$post['email'] || !validate::is_email($post['email'])) {
            redirect('member_post_empty_email');
        }
        if($post['groupid'] && $post['groupid'] < 0) {
            redirect('member_post_empty_group');
        }
        if(!$isedit) {
            if($this->check_username_exists($post['username'])) 
                redirect('member_post_exists_name');
            if(!$this->modcfg['existsemailreg'])
                if($this->check_email_exists($post['email'])) 
                    redirect('member_post_exists_email');
        }
    }

    function check_username_exists($username, $without_uid = null) {
        $this->db->from($this->table);
        $this->db->where('username',$username);
        if($without_uid > 0) $this->db->where_not_equal('uid', $without_uid);
        return $this->db->count() > 0;
    }

    function check_email_exists($email, $without_uid = null) {
        $this->db->from($this->table);
        $this->db->where('email',$email);
        if($without_uid > 0) $this->db->where_not_equal('uid', $without_uid);
        return $this->db->count() > 0;
    }

    function check_username($username, $echo = FALSE) {
        if(strlen($username) > 15) {
            if($echo) {echo lang('member_reg_name_len_max'); exit;}
            return redirect('member_reg_name_len_max');
        }
        $guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
        if(preg_match("/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&]|$guestexp/is", $username)) {
            if($echo) {echo lang('member_reg_name_sensitive_char'); exit;}
            redirect('member_reg_name_sensitive_char');   
        }
        if($censorwords = $this->modcfg['censoruser'] ? explode("\r\n", $this->modcfg['censoruser']) : '') {
            foreach($censorwords as $censor) {
                $preg = "/".str_replace("*", ".*?", $censor)."/is";
                if(preg_match($preg, $username)) {
                    if($echo) {echo lang('member_reg_name_limit'); exit;}
                    redirect('member_reg_name_limit');
                }
            }
        }
    }
    
    function check_password($password, $return_error = FALSE) {
        $len = 6;
        if(strlen($password) < $len) {
        	$error = sprintf(lang('member_post_empty_pw_len'), $len);
        	if($return_error) return $error;
            redirect($error);
        }
        if(!preg_match("/[a-zA-Z0-9_~!@#]+/i", $password)) {
        	if($return_error) return lang('member_reg_pw_limit');
            redirect('member_reg_pw_limit');
        }
    }

    function _send_salutatory_msg($uid) {
        if(!$member = $this->read($uid)) return;
        $subject = lang('member_reg_msg_subject', $this->global['cfg']['sitename']);
        $content = $this->modcfg['salutatory_msg'] ? $this->modcfg['salutatory_msg'] : lang('member_reg_msg_content');

        $rp = array('$sitename', '$username', '$time');
        $sm = array($this->global['cfg']['sitename'], $member['username'], date("Y-m-d H:i:s", $this->global['timestamp']));
        $content = str_replace($rp, $sm, $content);

        $MSG =& $this->loader->model('member:message');
        $MSG->send(0, $uid, $subject, $content);
        unset($MSG);
    }
}
?>