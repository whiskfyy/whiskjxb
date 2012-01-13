<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

class query_item {

    function category($params) {
        extract($params);
        $loader =& _G('loader');
        $pid = (int) $pid;
        if($pid > 0) {
            $C =& $loader->model('item:category');
            $root_id = $C->get_parent_id($pid);
            if(!$category = $loader->variable('category_' . $root_id, 'item')) return '';
        } else {
            $category = $loader->variable('category','item');
        }
        $result = '';
        foreach($category as $key => $val) {
            if($val['pid'] == $pid && $val['enabled']) {
                $result[$key] = $val;
            }
        }
        return $result;
    }

    function hotcategory($params) {
    }

    function subject($params) {
        extract($params);
        $loader =& _G('loader');
        $S =& $loader->model('item:subject');

        $S->db->select($select?$select:'*');
        $S->db->from($S->table);
        if($pid>0) $S->db->where('pid', $pid);
        if($catid>0) $S->db->where('catid', $catid);
        if($aid>0) $S->db->where('aid', $aid);
        if($finer>0) $S->db->where_more('finer', $finer);
        $S->db->where('status', 1);
        $orderby && $S->db->order_by($orderby);
        $S->db->limit($start, $rows);
        if(!$r = $S->db->get()) { return null; }

        $result = array();
        while($v = $r->fetch_array()) {
            $result[] = $v;
        }
        $r->free_result();
        return $result;
    }

    function review($params) {
        extract($params);
        $loader =& _G('loader');
        $db =& _G('db');
        if(!$select) $select = 'rid,pcatid,sid,uid,username,ip,title,content,sort1,sort2,sort3,sort4,sort5,sort6,sort7,sort8,price,enjoy,posttime';
        foreach(explode(',', $select) as $v) {
            if(!trim($v)) continue; $db->select('r.' . $v);
        }
        if($select_subject) {
            $db->join('dbpre_review','r.sid','dbpre_subject','s.sid', 'LEFT JOIN');
            foreach(explode(',', $select_subject) as $v) {
                if(!trim($v)) continue; $db->select('s.' . $v);
            }
        } else {
            $db->from('dbpre_review','r');
        }
        if($pid>0) $db->where('r.pcatid', $pid);
        if($id>0) $db->where('r.id', $id);
        if($uid>0) $db->where('r.uid', $uid);
        if(isset($best)) $db->where('r.best', (int)$best);
        if(isset($digst)) $db->where('r.digst', (int)$digst);
        if(isset($havepic)) $db->where('r.havepic', (int)$havepic);
        $db->where('r.status', 1);
        $orderby && $db->order_by($orderby);
        $db->limit($start, $rows);

        if(!$r = $db->get()) { return null; }
        $result = array();
        while($v = $r->fetch_array()) {
            $result[] = $v;
        }
        $r->free_result();
        return $result;
    }

    function picture($params) {
        extract($params);
        $loader =& _G('loader');

        $S =& $loader->model('item:picture');
        $S->db->from($S->table);
        $S->db->select($select?$select:'*');
        if($sid>0) $S->db->where('sid', $sid);
        if($uid>0) $S->db->where('uid', $uid);
        $S->db->where('status', 1);
        $orderby && $S->db->order_by($orderby);
        $S->db->limit($start, $rows);

        if(!$r=$S->db->get()) { return null; }
        $result = array();
        while($v = $r->fetch_array()) {
            $result[] = $v;
        }
        $r->free_result();
        return $result;
    }

    function tag($params) {
        $loader =& _G('loader');
        extract($params);

        $TAG =& $loader->model('item:tag');
        $TAG->db->select($select?$select:'*');
        $TAG->db->from($TAG->table);
        $TAG->db->where('closed', 0);
        $orderby && $TAG->db->order_by($orderby);
        $TAG->db->limit($start, $rows);

        if(!$r=$TAG->db->get()) { return null; }
        $result = array();
        while($v = $r->fetch_array()) {
            $result[] = $v;
        }
        $r->free_result();
        return $result;
    }

    function options($params) {
        extract($params);
        if(!$value) return '';
        $loader =& _G('loader');
        $fields = $loader->variable('field_'.$modelid, 'item');
        foreach($fields as $_val) {
            if($_val['fieldname'] == $fieldname && $_val['type'] == 'option') {
                $options = $_val['config']['value'];
            }
        }
        if(!$options) return $value;
        $result = array();
        $__val = explode(",", $value);
        $list = explode("\r\n", preg_replace("/\s*(\r\n|\n\r|\n|\r)\s*/", "\r\n", $options));
        foreach($list as $sval) {
            $v = explode("=",$sval);
            if($__val && in_array($v[0], $__val)) {
                $result[] = $v[1];
            }
        }
        return $result;
    }

    //catid
    function reviewopt($params) {
        extract($params);
        $loader =& _G('loader');
        $result = array();
        if($catid > 0 && !$modelid) {
            $category = $loader->variable('category', 'item');
            if(isset($category[$catid])) {
                if($category[$catid]['pid'] > 0) {
                    $pid = $category[$catid]['pid'];;
                } else {
                    $pid = $catid;
                }
            }
            $modelid = $category[$pid]['modelid'];
            $rogid = $category[$pid]['review_opt_gid'];
        }
        $result = $loader->variable('review_opt_' . $rogid, 'item');
        return $result;
    }

    //catid
    function attlist($params) {
        extract($params);
        $loader =& _G('loader');
        $result = array();
        if(!$catid = abs((int)$catid)) return null;
        $atts = $loader->variable('att_list_'.$catid, 'item');
        return $atts;

    }


}
?>