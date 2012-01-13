<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

class query {

    function sql($params) {
        extract($params);
        if(!$sql) echo lang('global_sql_empty');
        $sql = str_replace('dbpre_', _G('dns','dbpre'),$sql) . ' LIMIT '.$start.','.$rows;
        $db =& _G('db');
        if(!$q = $db->query($sql)) { return null; }
        $result = array();
        while($value = $q->fetch_array()) {
            $result[] = $value;
        }
        $q->free_result();
        return $result;
    }

    function table($params) {
        $db =& _G('db');
        extract($params);
        if(!$table) echo lang('global_sql_invalid','from');
        $table = str_replace('dbpre_', _G('dns','dbpre'), $table);
        $select = $select ? $select : '*';
        $where = $where ? ('WHERE ' . $where) : '';
        $groupby = $groupby ? ('GROUP BY ' . $groupby) : '';
        $orderby = $orderby ? ('ORDER BY ' . $orderby) : '';
        $sql = "SELECT $select FROM $table $where $groupby $orderby LIMIT $start,$rows";
        if(!$q = $db->query($sql)) { return null; }
        $result = array();
        while($value = $q->fetch_array()) {
            $result[] = $value;
        }
        $q->free_result();
        return $result;
    }

    function area($params) {
        extract($params);
        $loader =& _G('loader');
        if($city) {
            $city = $city;
        } elseif(!$pid) {
            $city = 1;
            $pid = 0;
        } else {
            $rel = $loader->variable('area_rel');
            if(!$rel[$pid]) return '';
            list($paid, $level)= explode(':', $rel[$pid]);
            if($level == 3) {
                list($city,) = explode(':', $rel[$paid]);
            } else if($level == 2) {
                $city = $paid;
            } else {
                $city = $pid;
            }
        }
        $area = $loader->variable('area_' . $city);
        $pid = $pid > 0 ? $pid : 1;
        $result = array();
        foreach($area as $key => $val) {
            if($val['pid'] == $pid) {
                $result[] = $val;
            }
        }
        return $result;
    }

    function bcastr($params) {
        extract($params);
        $loader =& _G('loader');
        $db =& _G('db');
        $db->from('dbpre_bcastr');
        $db->where('groupname', $groupname);
        $db->order_by('orders');
        if(!$q = $db->get()) return array();
        $result = array();
        while($v=$q->fetch_array()) {
            $result[] = $v;
        }
        $q->free_result();
        return $result;
    }


}
?>