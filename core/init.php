<?php
/**
* System init
* @author moufer<moufer@163.com>
* @copyright (C)2001-2007 Moufersoft
*/
define('IN_MUDDER', TRUE);
define('DEBUG', FALSE);
define('DS', DIRECTORY_SEPARATOR);
define('MUDDER_DOMAIN', $_SERVER['HTTP_HOST']?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME']);
define('MUDDER_CORE', dirname(__FILE__) .  DS);
define('MUDDER_ROOT', dirname(MUDDER_CORE) . DS);
define('MUDDER_DATA', MUDDER_ROOT . 'data' . DS);
define('MUDDER_CACHE', MUDDER_DATA . 'cachefiles' . DS);
define('MUDDER_MODULE', MUDDER_CORE . 'modules' . DS);
define('MUDDER_PLUGIN', MUDDER_CORE . 'plugins' . DS);

if(DEBUG) {
    error_reporting(E_ALL ^ E_NOTICE);
    @ini_set('display_errors', 'On');
} else {
    error_reporting(0);
    @ini_set('display_errors', 'Off');
}

if(function_exists('set_magic_quotes_runtime')) {
    @set_magic_quotes_runtime(0);
}
$_G = $_C = $_CFG = $_HEAD = $_QUERY = $MOD = array();

$_G['mtime'] = explode(' ', microtime());
$_G['starttime'] = $_G['mtime'][1] + $_G['mtime'][0];

require MUDDER_DATA . 'config.php';
require MUDDER_CORE . 'version.php';

//timezone
if(function_exists('date_default_timezone_set')) {
    @date_default_timezone_set('Etc/UTC');
    $_G['timestamp'] = time() + $_G['timezone'] * 3600;
    //使用和时间相关的函数(例如filemtime，系统已定义自己的函数myfilemtime),需要在获取的时间上加上 filemtime(file) + $_G['timezone'] * 3600;
} else {
    $_G['timestamp'] = time();
}

header('Content-type: text/html; charset=' . $_G['charset']);
if($_G['attackevasive'] && (!defined('IN_ADMIN') || SCRIPTNAV != 'seccode')) {
    include MUDDER_CORE . 'fense.php';
}

require MUDDER_CORE . 'function.php'; // global function
require MUDDER_CORE . 'loader.php';
$_G['loader'] = new ms_loader();

// web info
$_G['web'] = get_webinfo();
$_G['ip'] = get_ip();
define('SELF', $_G['web']['self']);
define('URLROOT', get_urlroot());

$_G['loader']->lib('base', NULL, FALSE);
$_G['loader']->lib('model', NULL, FALSE);

$_G['cookie'] = $_G['loader']->cookie();
$_C =& $_G['cookie'];
$_G['loader']->helper('cache');
$_G['cfg'] = read_cache(MUDDER_CACHE . 'modoer_config.php');
$_G['modules'] = read_cache(MUDDER_CACHE . 'modoer_modules.php');
if(!$_G['cfg'] || !$_G['modules']) {
    include MUDDER_CORE . 'cache.php';
    $_G['modules'] = read_cache(MUDDER_CACHE . 'modoer_modules.php');
    foreach(array_keys($_G['modules']) as $flag) {
        $file = MUDDER_MODULE . $flag . DS . 'inc' . DS . 'cache.php';
        if(is_file($file)) include $file;
    }
    show_error('global_cache_succeed');
}
$_MODULES =& $_G['modules'];
$_CFG =& $_G['cfg'];
$_G['cfg']['siteurl'] = $_G['cfg']['siteurl'] . (substr($_G['cfg']['siteurl'] , -1) != '/' ? '/' : '');

if($_G['cfg']['siteclose'] && !defined('IN_ADMIN') && $_GET['act'] != 'seccode') {
    show_error($_G['cfg']['closenote']);
}
if($_G['cfg']['useripaccess'] && !check_ipaccess($_G['cfg']['useripaccess'])) {
    show_error(lang('global_ip_without_list'));
}
if($_G['cfg']['ban_ip'] && check_ipaccess($_G['cfg']['ban_ip'])) {
    show_error(lang('global_ip_not_have_access'));
}

//ob
if($_G['cfg']['gzipcompress'] && function_exists('ob_gzhandler')) {
    @ob_start('ob_gzhandler');
} else {
    $_G['cfg']['gzipcompress'] = 0;
    ob_start();
}

if(PHP_VERSION < '4.1.0') {
    $_GET = $HTTP_GET_VARS;
    $_POST = $HTTP_POST_VARS;
    $_COOKIE = $HTTP_COOKIE_VARS;
    $_SERVER = $HTTP_SERVER_VARS;
    $_ENV = $HTTP_ENV_VARS;
    $_FILES = $HTTP_POST_FILES;
}
unset($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_ENV_VARS, $HTTP_POST_FILES);

//URL rewrite
if($_G['cfg']['rewrite']) {
    $_G['rewriter'] =& $_G['loader']->lib('urlrewriter');
    $_G['rewriter']->set_mod($_G['cfg']['rewrite_mod']);
    $_G['rewriter']->hide_index = $_G['cfg']['rewrite_hide_index'];

    if(isset($_GET['Rewrite'])) {
        $_G['rewriter']->html_recover($_GET['Rewrite']);
    } elseif(isset($_GET['Pathinfo'])) {
        $_G['rewriter']->pathinfo_recover($_GET['Pathinfo']);
    } elseif($_G['index_url_rewrite']) {
        $_G['rewriter']->index_recover();
    }
}

define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
if(MAGIC_QUOTES_GPC) {
    $_POST = strip_slashes($_POST);
    $_GET = strip_slashes($_GET);
    $_COOKIE = strip_slashes($_COOKIE);
    $_REQUEST = strip_slashes($_REQUEST);
}

if(!defined('IN_ADMIN')) {
    $_POST = strip_sql($_POST);
    $_GET = strip_sql($_GET);
    $_COOKIE = strip_sql($_COOKIE);
    $_REQUEST = strip_sql($_REQUEST);
}

// ajax
if($_G['in_ajax'] = isset($_POST['in_ajax']) ? $_POST['in_ajax'] : (isset($_GET['in_ajax']) ? $_GET['in_ajax'] :  0)) {
    define('IN_AJAX', TRUE);
}

//二/三级域名处理
if('http://'.$_G['web']['domain'].'/' != $_G['cfg']['siteurl'] && !$_G['in_ajax']) {
    include MUDDER_DATA . 'config_domain.php';
    if($_G['sldomain_level']) foreach($_G['sldomain_level'] as $_sldomain_hook_file) {
        if(is_file(MUDDER_CORE . $_sldomain_hook_file)) {
            $_sldomain_fun = include(MUDDER_CORE . $_sldomain_hook_file);
            if($_sldomain_fun()) {
                $_G['sldomain'] = TRUE;
                break;
            }
        }
    }
    /*
    if(!$_G['sldomain']) {
        header( "HTTP/1.1 301 Moved Permanently" );
        header( "Location: " . $_G['cfg']['siteurl'] );
        exit;
    }
    */
}

/*
if(!$in_ajax && function_exists('get_headers')) {
    if($headers = @get_headers($php_self, 1)) {
        $_G['in_ajax'] = $headers['X-Requested-With'] == 'XMLHttpRequest';
    }
}
*/

//init hook
foreach($_G['modules'] as $__module_flag) {
    $__module_hook_file =  MUDDER_MODULE . $__module_flag['flag'] . DS . 'inc' . DS . 'init_hook.php';
    if(is_file($__module_hook_file)) include $__module_hook_file;
}
unset($__module_flag,$__module_hook_file);

// user
if(!defined('IN_ADMIN')) {
    $_G['user'] =& $_G['loader']->model('member:user');
    $_G['user']->auto_login();
    $user =& $_G['user'];
    //login access
    if($user->get_access('member_forbidden') && $_GET['act'] != 'login') {
        show_error('member_access_forbidden');
    }
}

/*
$apache_mod = array();
if(function_exists('apache_get_modules')) {
    $apache_mod = apache_get_modules();
    if (!in_array('mod_rewrite', $apache_mod)) {
        $_CFG['rewrite_enable'] = 0;
    }
}
unset($apache_mod);
*/

// mutipage init
$_GET['page'] = (int) _get('page');
$_GET['page'] = $_GET['page'] < 1 ? 1 : $_GET['page'];
$_GET['offset'] = (int) _get('offset');
$_GET['offset'] = $_GET['offset'] < 1 ? 20 : $_GET['offset'];

// header init
$_HEAD['title'] = $_G['cfg']['sitename'] . $_G['cfg']['titlesplit'] . $_G['cfg']['subname'];
$_HEAD['keywords'] = $_G['cfg']['meta_keywords'];
$_HEAD['description'] = $_G['cfg']['meta_description'];
$_HEAD['css'] = '';
$_HEAD['js'] = '';

// rand
$_G['random'] = random(5);

// datacall
$_G['datacall'] =& $_G['loader']->model('datacall');
$_G['datacall']->plan_delete();