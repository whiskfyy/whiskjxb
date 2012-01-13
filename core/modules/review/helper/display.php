<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
class display_review {

    //获取点评对象的接口信息
    //参数 idtype,keyname
    function typeinfo($params) {
        extract($params);
        if(!$keyname) $keyname = 'name';
        if(!$idtype) return '';
        $loader =& _G('loader');
        $R =& $loader->model(':review');
        if(!$typeinfo = $R->get_type($idtype)) return '';
        return $typeinfo[$keyname];
    }

    //获取点评对象的内容页地址
    //参数 idtype，id
    function typeurl($params) {
        extract($params);
        if(!$idtype) return '';
        if(!$id) return '';
        $loader =& _G('loader');
        $R =& $loader->model(':review');
        if(!$typeinfo = $R->get_type($idtype)) return '';
        return url(str_replace('_ID_',$id,$typeinfo['detail_url']));
    }

}
?>