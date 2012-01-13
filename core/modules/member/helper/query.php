<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
class query_member {

    function detail($params=null) {
        extract($params);

        if(!$uid && !$username) return false;

        $loader =& _G('loader');
        $db =& _G('db');
        
        $db->from('dbpre_members');
        $db->select($select?$select:'uid,username,email,groupid,coin,point,reviews,subjects,responds,flowers');
        if($uid) $db->where('uid', $uid);
        if($username) $db->where('username', $username);
        $result[] = $db->get_one();
        return $result;
    }

}
?>