<?php
/**
* 后台控制面板
* @author moufer<moufer@163.com>
* @copyright Moufer Studio(www.modoer.com)
*/
define('IN_ADMIN', TRUE);
define('SCRIPTNAV', 'admincp');

require dirname(__FILE__).'/core/init.php';

define('MUDDER_ADMIN', MUDDER_CORE . 'admin' . DS);

$_G['loader']->model('admin',FALSE);
$_G['admin'] =& $_G['loader']->model('cpuser');
$admin =& $_G['admin'];

if(_get('logout')) {
    $admin->logout();
    exit;
}

if(empty($admin->access)) {
    if(!$_POST['loginsubmit']) {
        include MUDDER_ADMIN.'cplogin.inc.php';
        exit;
    } else {
        $admin->login();
    }
} elseif($admin->access == '1') {
    if(!_post('admin_password') || (md5(_post('admin_password')) != $admin->password)) {
        include MUDDER_ADMIN . 'cplogin.inc.php';
        exit;
    } else {
        $admin->update_sessions();
        redirect('admincp_login_wait', SELF);
    }
} elseif($admin->access == '2') {
    redirect('admincp_login_op_without', SELF.'?logout=yes');
} elseif($admin->access == '3') {
    redirect('admincp_cpuser_colsed', SELF.'?logout=yes');
}

if(empty($admin->id) || $admin->id < 0 || !$admin->isLogin) {
    redirect('admincp_not_login', SELF);
}

$module = _input('module');
$act = _input('act');
$in_ajax = 0;
$in_ajax = _input('in_ajax');

$_G['loader']->helper('form');

if(empty($module) || $module == 'modoer') {
    $module = 'modoer';
    if(empty($act)) {
        $tab = 'home';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html><head>
<title><?=lang('admincp_title')?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$_G['charset']?>">
<script type="text/javascript" src="./static/javascript/jquery.js"></script>
<script type="text/javascript" src="./static/javascript/admin.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$(document).keydown(resetEscAndF5);
});
</script>
</head>
<body style="margin: 0px" scroll="no">
<div style="position: absolute;top: 0px;left: 0px; z-index: 2;height: 55px;width: 100%">
<iframe frameborder="0" id="header" name="header" src="<?=cpurl('modoer','cpheader')?>" scrolling="no" style="height: 55px; visibility: inherit; width: 100%; z-index: 1;"></iframe>
</div>
<table border="0" cellPadding="0" cellSpacing="0" height="100%" width="100%" style="table-layout: fixed;">
<tr><td width="173" height="55"></td><td></td></tr>
<tr>
<td width="173"><iframe frameborder="0" id="menu" name="menu" src="<?=SELF?>?module=modoer&act=cpmenu&tab=<?=$tab?>" scrolling="auto" style="height:100%;visibility:inherit;width:100%;z-index:1;overflow-x:hidden;overflow-y:auto; "></iframe></td>
<td width="*"><iframe frameborder="0" id="main" name="main" src="<?=cpurl('modoer','cphome')?>" scrolling="yes" style="height: 100%; visibility: inherit; width: 100%; z-index: 1;overflow: auto;"></iframe></td>
</tr></table>
</body>
</html>
<?php
        exit(0);
    }
    if(!$admin->check_access('modoer') && !in_array($act, array('cpheader','cpmenu','cphome','help','admin'))) redirect('global_op_access');
    $actfile = MUDDER_ADMIN . $act . '.inc.php';
    if(!is_file($actfile)) {
        show_error(lang('global_file_not_exist', '[ADMIN_DIR]' . DS . $act . '.inc.php'));
    }
    include $actfile;
    $acts = array('cpheader','cpmenu');
    if(!$in_ajax && !in_array($act,$acts)) cpheader();
    if($admin->tplname) {
        if(!is_file(MUDDER_CORE . $admin->tplname)) {
            show_error(sprintf(lang('global_file_not_exist'), $admin->tplname));
        }
        include MUDDER_CORE . $admin->tplname;
    }
    if(!$in_ajax && !in_array($act,$acts)) cpfooter();
    
} elseif(isset($_G['modules'][$module])) {
    if(!$admin->check_access($module)) redirect('global_op_access');
    $adminfile_path = 'modules' . DS . $module;
    require_once MUDDER_CORE . $adminfile_path . DS . 'common.php';

    if(preg_match("/^[0-9a-z\_\.]+$/i", $act)) {
        $actfile = MOD_ROOT . 'admin' . DS . $act.'.inc.php';
        if(!is_file($actfile)) {
            show_error(lang('global_file_not_exist', $_G['modules'][$module]['directory'] . DS . 'admin' . DS . $act . '.inc.php'));
        }
        include $actfile;
        if(!$in_ajax) cpheader();
        if($admin->tplname) {
            if(!is_file(MUDDER_CORE . $admin->tplname)) {
                show_error(lang('global_file_not_exist', $admin->tplname));
                include MUDDER_CORE . $admin->tplname;
            }
            include MUDDER_CORE . $admin->tplname;
        }
        if(!$in_ajax) cpfooter();
    } else {
        show_error(lang('global_op_unkown'));
    }
} else {
    show_error(lang('global_not_found_module', $module));
}

function cpurl($module='modoer',$act='',$op='',$param=null) {
    $url = SELF . '?module=' . $module . '&act=' . $act;
    if($op) {
        $url .= '&op=' . $op;
    }
    if($param) foreach($param as $k=>$v) {
        if(in_array($k,array('module','act','op'))) unset($param[$k]);
    }
    if(is_array($param) && $param) {
        $url .= '&' . url_implode($param);
    } elseif(is_string($param && $param)) {
        $url .= '&' . _T($param);
    }
    return $url;
}

function cptpl($file, $module=NULL) {
    global $_G;
    if($module && isset($_G['modules'][$module])) {
        $path = 'modules' . DS . $module . DS . 'admin' . DS;
    } elseif($module) {
        lang('global_not_found_module', $module);
    } else {
	    $path = 'admin' . DS;
    }
    $filename = $path . 'templates' . DS . $file . '.tpl.php';
    return $filename;
}

function cpmsg($msg, $url = 'javascript:history.go(-1);', $min='3') {
    global $_G;
    $message = trim($msg);
	if(is_array($url)) {
		$navs = $url;
		$url_forward = str_replace('&amp;', '&', trim($url[0]['url']));
	} else {
		$url_forward = str_replace('&amp;', '&', trim($url));
	}
    $min = (int) $min;
    cpheader(0);
    include MUDDER_CORE . cptpl('cpmsg');
    cpfooter(0);
    exit;
}

function cpheader($js=1) {
	global $_G;
	print <<< EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=$_G[charset]">
<meta http-equiv="x-ua-compatible" content="ie=7" />
<link rel="stylesheet" type="text/css" href="./static/images/admin/admin.css">

EOT;
    if($js) {
    print <<< EOT
<script type="text/javascript" src="./data/cachefiles/config.js"></script>
<script type="text/javascript" src="./static/javascript/jquery.js"></script>
<script type="text/javascript" src="./static/javascript/common.js"></script>
<script type="text/javascript" src="./static/javascript/admin.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$(document).keydown(resetEscAndF5);
});
</script>
</head>
<body>

EOT;
    }
}

function cpfooter($info=1) {
    global $_G, $_CFG;
    $mtime = explode(' ', microtime());
    $totaltime = number_format(($mtime[1] + $mtime[0] - $_G['starttime']), 6);
    $gzip = $_CFG['gzipcompress'] ? 'enabled' : 'disabled';
    $sitedebug = 'Processed in '.$totaltime.' second(s), '.$_G['db']->query_num.' queries, Gzip '.$gzip;
    $version = $_G['modoer']['version'];
    $DEBUG = '';
    if(DEBUG) {
	    $DEBUG .= $_G['db']->debug_print();
        $DEBUG .= $_G['loader']->debug_print();
    }
    if($info) {
	print <<< EOT
<div id="footer">
<small>Powered by <a href="http://www.modoer.com" target="_blank">Modoer $version</a> &copy; 2007 - 2010, <a href="http://www.modoer.com" target="_blank">Moufer Studio</a></small><br />
<small>$sitedebug</small>
</div>
EOT;
    }
    print <<< EOT
$DEBUG
</body>
</html>
EOT;
}
?>