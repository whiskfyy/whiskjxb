<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
(!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied');

$op = _input('op');
switch($op) {
case 'phpinfo':
    if(!function_exists('phpinfo')) {
        redirect('admincp_tool_phpinfo_not_exists');
    }
    $in_ajax = 1;
    phpinfo();
    break;
case 'cache':
    if($_POST['dosubmit']) {
        if(!$_POST['cache']) redirect('global_op_unselect');
        foreach ($_POST['cache'] as $key => $value) {
            $fun = 'tool_update_' . $value;
            $fun();
        }
        redirect('global_op_succeed', cpurl($module, $act, $op));
    } else {
        $admin->tplname = cptpl('tool_cache');
    }
    break;
}

function tool_update_setting() {
    include MUDDER_CORE . 'cache.php';
    $db =& _G('db');
    $db->from('dbpre_modules');
    $db->where('disable',0);
    $row = $db->get();
    while ($value = $row->fetch_array()) {
        $file = MUDDER_MODULE . $value['flag'] . DS . 'inc' . DS . 'cache.php';
        if(is_file($file)) {
            include $file;
        }
    }
}

function tool_update_template() {
    $files = array();
    $cachedir = MUDDER_DATA . 'templates';
    if ($handle = @opendir($cachedir)) {
        while (false !== ($file = @readdir($handle))) {
            if (preg_match('/^.+\.tpl\.php$/i', $file)) {
                $files[] = $file;
            }
        }
        closedir($handle);
    }
    if($files) foreach($files as $filename) {
        @unlink($cachedir . DS . $filename);
    }
}

function tool_update_datacall() {
    $loader =& _G('loader');
    $D =& $loader->model('datacall');
    $D->delete_datacall_cache_all();
}
?>