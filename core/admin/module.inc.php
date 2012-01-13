<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
(!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied');

$MM =& $_G['loader']->model('module');
$op = $_GET['op'] ? $_GET['op'] : $_POST['op'];

switch($op) {
    case 'cache':
        $MM->write_cache();
        $C =& $_G['loader']->model('config');
        if(is_array($_POST['moduleflags'])) {
            foreach($_POST['moduleflags'] as $val) {
                $C->write_cache($val);
            }
            redirect('global_op_succeed', cpurl($module, $act, 'manage'));
        } else {
            redirect('admincp_module_unselect');
        }
        break;
    case 'listorder':
        $MM->listorder($_POST['modules']);
        redirect('global_op_succeed', cpurl($module, $act, 'manage'));
        break;
    case 'info':
        if(!$_POST['dosubmit']) {
            $moduleid = (int)$_GET['moduleid'];
            $moduleinfo = $MM->read($moduleid);
            unset($moduleinfo['config']);
            $admin->tplname = cptpl('module_info');
        } else {
            $moduleinfo = $_POST['moduleinfo'];
            $moduleinfo['moduleid'] = (int) $moduleinfo['moduleid'];

            if(!$moduleinfo['name']) redirect('admincp_module_empty_name');
            if(!$moduleinfo['moduleid']) redirect('admincp_module_emoty_id');

            $MM->update(array('name'=>$moduleinfo['name']), $moduleinfo['moduleid']);
            redirect('global_op_succeed', cpurl($module, $act));
        }
        break;
    case 'enable':
    case 'disable':
        $moduleid = (int) $_GET['moduleid'];
        $disable = $op=='enable' ? 0 : 1;
        $MM->update(array('disable'=>$disable), $moduleid);
        redirect('global_op_succeed', cpurl($module, $act));
        break;
    case 'versioncheck':
        $moduleid = (int) $_GET['moduleid'];
        if(!$newversion = $MM->versioncheck($moduleid)) {
            redirect('admincp_module_versioncheck_err');
        }
        $admin->tplname = cptpl('module_checkup');
        break;
    case 'install':
        if(!$admin->is_founder) redirect('global_op_access');
        $step = empty($_POST['step']) ? 1 : $_POST['step'];
        switch($step) {
            case 2:
                $newmodule = $MM->install_check($_POST['directory']);
                $readonly = array('flag', 'reliant', 'author', 'siteurl', 'email', 'copyright');
                break;
            case 3:
                $newmodule = $_POST['newmodule'];
                empty($newmodule['flag']) and redirect('admincp_module_install_empty_dir');

                $dir = MUDDER_MODULE . $newmodule['flag'];
                // 调用模块安装前的系统检测文件
                if(is_file($checkfile = $dir . DS . 'install' . DS . 'install_check.php')) {
                    include $checkfile;
                }
                unset($checkfile);

                // 安装数据表
                if(is_file($sqlfile = $dir . DS . 'install' . DS . 'module_install.sql')) {
                    $fp = fopen($sqlfile, 'rb');
                    $modulesql = fread($fp, 2048000);
                    fclose($fp);
                }

                if($modulesql) {
                    $_G['loader']->helper('sql');
                    sql_run_query($modulesql);
                }
                // 调用模块安装文件
                if(is_file($installfile = $dir . DS . 'install' . DS . 'install.php')) {
                    include $installfile;
                }
                unset($installfile);

                $cfgfile = $dir . DS . 'install' . DS . 'config.php';
                $MM->install($cfgfile, $newmodule);

                redirect(sprintf(lang('admincp_module_install_succeed'), $newmodule['name']), cpurl($module, $act, 'manage'));
        }
        $admin->tplname = cptpl('module_install');
        break;
    case 'unstall':
        $moduleid = (int) $_GET['moduleid'];
        $moduleinfo = $MM->read($moduleid);

        if($moduleinfo['iscore']) {
            redirect('admincp_module_uninstall_core');
        }
        
        if($MM->reliant_check($moduleinfo['flag'],1)) {
            redirect('admincp_module_uninstall_exist_reliant');
        }

        $dir = MUDDER_MODULE . $moduleinfo['flag'];
        // 执行卸载数据表SQL
        if(is_file($sqlfile = $dir . DS . 'install' . DS . 'module_uninstall.sql')) {
            $fp = fopen($sqlfile, 'rb');
            $modulesql = fread($fp, 2048000);
            fclose($fp);
        }
        $_G['loader']->helper('sql');
        $modulesql && sql_run_query($modulesql);
        // 调用模块卸载文件
        if(is_file($uninstallfile = $dir . DS . 'install' . DS . 'uninstall.php')) {
            @include($uninstallfile);
        }
        unset($uninstallfile);
        $MM->uninstall($moduleinfo);

        redirect(sprintf(lang('admincp_module_uninstall_succeed'), $moduleinfo['name']), cpurl($module, $act, 'manage'));
        break;
    default:
        $op = 'manage';
        $list = $MM->read_all();
        $admin->tplname = cptpl('module');
}

?>