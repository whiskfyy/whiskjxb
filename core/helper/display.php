<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
class display {

    //参数 aid,keyname
    function area($params) {
        extract($params);
        if(!$aid) return '';
        if(!$keyname) $keyname = 'name';
        $loader =& _G('loader');
        $city = 1;
        $area = $loader->variable('area_' . $city);
        if(!isset($area[$aid][$keyname])) return '';
        return $area[$aid][$keyname];
    }

}
?>