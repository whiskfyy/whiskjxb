<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$module_flag = $_GET['module_flag'];
if(!check_module($module_flag)) redirect('global_search_module_empty');
$search_hook = 'modules' . DS . $module_flag . DS . 'inc' . DS . 'search_hook.php';
if(!file_exists(MUDDER_CORE . $search_hook)) redirect(lang('global_file_not_exist', $search_hook));
include MUDDER_CORE . $search_hook;
?>