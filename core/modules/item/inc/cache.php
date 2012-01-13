<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

inc_cache_item();

function inc_cache_item() {
    $loader =& _G('loader');

    $tmp =& $loader->model('config');
    $tmp->write_cache('item');

    $cachelist = array('model','field','review_opt','taggroup','category','att_cat','review_opt_group');
    foreach ($cachelist as $value) {
        $tmp =& $loader->model('item:'.$value);
        $tmp->write_cache();
    }
}