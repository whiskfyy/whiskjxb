<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
class query_exchange {

    function new_exchange($params) {

        extract($params);
        $loader =& _G('loader');
        $db =& _G('db');

        $db->join('dbpre_exchange_log', 'e.giftid', 'dbpre_exchange_gifts', 'g.giftid', 'LEFT JOIN');
        $db->select($select?$select:'e.giftid,e.username,g.name,g.thumb,g.price');
        $db->where_less('e.status',3);
        $db->group_by('e.giftid');
        $db->order_by('e.exchangetime','DESC');
        $db->limit($start, $rows);

        if(!$r=$db->get()) { return null; }
        $result = array();
        while($v = $r->fetch_array()) {
            $result[] = $v;
        }
        $r->free_result();
        return $result;

    }

    function gifts($params) {
        extract($params);
        $loader =& _G('loader');
        $db =& _G('db');

        $db->from('dbpre_exchange_gifts');
        $db->select($select?$select:'giftid,name,price,thumb,salevolume');
        if($price>0) $db->where_more('price', $price);
        if($salevolume>0) $db->where_more('salevolume', $salevolume);
        $db->where('available',1);
        $db->order_by('displayorder');
        $db->limit($start, $rows);

        if(!$r=$db->get()) { return null; }
        $result = array();
        while($v = $r->fetch_array()) {
            $result[] = $v;
        }
        $r->free_result();
        return $result;
    }

}
?>