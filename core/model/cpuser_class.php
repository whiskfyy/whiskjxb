<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

class msm_cpuser extends msm_admin {

    var $isLogin = FALSE;
    var $access = '';
    var $tplname = '';

    var $id = 0;
    var $is_founder = 'N';

    function __construct() {
        parent::__construct();

        $admin_id = (int)$this->global['cookie']['adminid'];
        $admin_hash = $this->global['cookie']['adminhash'];

        if($admin_id && $admin_hash) {
            if($admin = $this->read($admin_id)) {
                $hash = $this->get_hash($admin['id'], $admin['password']);
                if($admin_hash == $hash) {
                    $this->_set_variable($admin);
                    $this->isLogin = TRUE;
                    $this->hash = $hash;
                    $this->access = $this->get_access();
                }
            }
        }
        if(!$this->isLogin) $this->errmsg = lang('admincp_login_invalid');
    }

    function msm_cpuser() {
        $this->__construct();
    }

    function login() {

        $username = _post('admin_name');
        $password = _post('admin_password');

        if(!$username || !$password) {
            redirect('admincp_login_invalid');
        }

        if(_G('cfg','console_seccode')) {
            check_seccode(_post('seccode'));
        }

        $admin_md5pwd = md5($password);
        $this->db->where('adminname', $username);
        $this->db->where('password', $admin_md5pwd);
        $this->db->from($this->table);
        if($result = $this->db->get()) {
            $row = $result->fetch_array();
            $result->free_result();
        }

        if($row) {
            $this->id = (int)$row['id'];
            $this->adminname = $row['adminname'];
            $this->hash = $this->get_hash($row['id'], $row['password']);
            $this->record_login();
            $nowaccess = $this->get_access();
            if($nowaccess == 1) {
                $this->db->from('dbpre_adminsessions');
                $this->db->set('errorcount','-1');
                $this->db->where('adminid', $this->id);
                $this->db->update();
            }
            set_cookie("adminid", $this->id);
            set_cookie("adminhash", $this->hash);
            redirect('admincp_login_wait', SELF);
        } else {
            $this->clearvar();
            redirect('admincp_login_invalid');
        }
    }

    function logout() {
        del_cookie(array("adminid", "adminhash"));
        if($this->id) {
            $this->db->from('dbpre_adminsessions');
            $this->db->where('adminid', $this->id);
            $this->db->delete();
            $this->clearvar();
        }
        redirect('admincp_logout_wait', SELF);
    }

    function clearvar() {
        $this->id = 0;
        $this->adminname = '';
        $this->email = '';
        $this->admintype = 0;
        $this->is_founder = false;
        $this->isLogin = false;
        $this->hash = '';
        $this->access = 0;
    }

    function get_access() {

        //删除已经过期的session
        $this->db->from('dbpre_adminsessions');
        $this->db->where_less("dateline", $this->global['timestamp'] - 3601);
        $this->db->delete();
		/*
        if(defined('IN_ADMIN') && $this->isLogin && _get('module') == 'modoer' && _get('act') == 'database' && _get('op') == 'reset') {
            return 10;
        }*/
        //++IP check
        if($this->closed) {
            return 3; //帐号停用
        } elseif($this->global['cfg']['adminipaccess'] && !check_ipaccess($this->global['cfg']['adminipaccess'])) {
            return 2; //IP filter
        } else {
            $this->db->select("errorcount");
            $this->db->from('dbpre_adminsessions');
            $this->db->where("adminid", $this->id);
            if($this->global['admincp']['checkip']) {
                $this->db->where("ip", $this->global['ip']);
            }
            $this->db->where_more("dateline", $this->global['timestamp'] - 3600);

            if($r = $this->db->get()) {
                $session = $r->fetch_array();
                if($session['errorcount'] == -1) {
                    $this->db->from($this->global['dns']['dbpre'] . 'adminsessions');
                    $this->db->set('dateline', $this->global['timestamp']);
                    $this->db->where('adminid', $this->id);
                    $this->db->update();
                    return 11; //OK
                } elseif($session['errorcount'] <= 3) {
                    return 1; //error
                } else {
                    return 0;
                }
            } else {
                // timeout
                $this->db->from($this->global['dns']['dbpre'] . 'adminsessions');
                $this->db->where('adminid', $this->id);
                $this->db->where_less('dateline', $this->global['timestamp'] - 3600);
                $this->db->delete();
                // insert
                $this->db->set(array (
                        'adminid' => $this->id,
                        'ip' => $this->global['ip'],
                        'dateline' => $this->global['timestamp'], 
                        'errorcount' => '0'
                    )
                );
                $this->db->from($this->global['dns']['dbpre'] . 'adminsessions');
                $this->db->insert();
                return 1;
            }
        }
    }

    function _set_variable(& $admin) {
        foreach($admin as $key => $val) {
            $this->$key = $val;
            if($key == 'mymodules' && $val!='') {
                $this->mymodules = explode(',',$val);
            }
        }
        $this->is_founder = $this->is_founder == 'Y';
        if($this->is_founder) {
            $this->mymodules = array('modoer');
            foreach($this->global['modules'] as $k => $v) {
                $this->mymodules[] = $v['flag'];
            }
        }
    }

    function get_hash($id, $pw) {
        return substr(md5($id . $pw . $this->global['cfg']['authkey']), 0, 16);
    }

    function record_login() {
        $this->db->from($this->table);
        $this->db->where('id', $this->id);
        $this->db->set('logintime', $this->global['timestamp']);
        $this->db->set('loginip', $this->global['ip']);
        $this->db->set_add('logincount', 1);
        $this->db->update();
    }

    function update_sessions() {
        $this->db->from('dbpre_adminsessions');
        $this->db->set('errorcount', '-1');
        $this->db->where('adminid', $this->id);
        $this->db->update();
    }

    function get_sessions() {
        $this->db->join('dbpre_adminsessions','ass.adminid','dbpre_admin','a.id');
        $this->db->select('ass.ip,ass.dateline,a.id,a.adminname');
        $this->db->order_by('ass.dateline','DESC');
        $query = $this->db->get();
        return $query;
    }

    function check_access($module='modoer',$act=null,$op=null) {
        return ($module=='ALL') || $this->is_founder || in_array($module,$this->mymodules);
    }

}
?>