<?php
define('DS', DIRECTORY_SEPARATOR);
define('IN_MUDDER', TRUE);
define('MUDDER_ROOT', dirname(__FILE__) . DS);
define('MUDDER_CORE', dirname(__FILE__) . DS . 'core' . DS);
define('CHARSET', 'utf-8');

header('Content-type: text/html; charset='.CHARSET);

error_reporting(7);
set_time_limit(3600);

$_G = array();

$configfile = MUDDER_ROOT . 'data' . DS . 'config.php';
if(!file_exists($configfile)) {
    $next_enable = ' disabled';
    $error = "发现 data/config.php 文件不存在，请将 data/config.new.php 文件更名为 data/config.php 然后再安装。";
    die($error);
}

$lockfile = MUDDER_ROOT.'data'.DS.'install.lock';
//file_exists($lockfile) && exit('Modoer 系统已安装，如需重新安装，请删除 ./data/install.lock 文件，同时清空缓存文件。');

include MUDDER_CORE . 'version.php';
include MUDDER_CORE . 'function.php';

$_G['web'] = get_webinfo();
$_G['ip'] = get_ip();
define('SELF', $_G['web']['self']);
define('MUDDER_URLPATH', str_replace('\\', '/', dirname(SELF)));

if(get_magic_quotes_gpc()) {
    $_POST = strip_slashes($_POST);
    $_GET = strip_slashes($_GET);
    $_COOKIE = strip_slashes($_COOKIE);
}

include MUDDER_CORE . DS . 'loader.php';
$_G['loader'] = new ms_loader();
$_G['loader']->lib('base', NULL, FALSE);
//$_G['loader']->lib('model', NULL, FALSE);

$sqlfile = MUDDER_ROOT.'install'.DS.'install.sql';
!is_readable($sqlfile) &&exit('Modoer 安装数据库文件 install/install.sql 不存在或者读取失败。');

$step = $_POST['step']?trim($_POST['step']):trim($_GET['step']);
empty($step) && $step = '1';

if($step == '1') {
    //运行环境监测
    $checkarr = array('name'=>'PHP版本','access'=>'4.4.8');
    if(phpversion()>='4.4.8') {
        $checkarr['currently'] = '<span class="font_3">√ '.phpversion().'</span>';
    } else {
        $next_enable = ' disabled';
        $checkarr['currently'] = '<span class="font_1">× '.phpversion().'</span>';
    }
    $accessevns[] = $checkarr;
    //GD库
    $checkarr = array('name'=>'GD库版本','access'=>'需开启');
    if(function_exists('gd_info')) {
        $checkarr['currently'] = '<span class="font_3">√ 已开启</span>';
    } else {
        $next_enable = ' disabled';
        $checkarr['currently'] = '<span class="font_1">× 未开启</span>';
    }
    $accessevns[] = $checkarr;
    //PHP短标记
    $checkarr = array('name'=>'PHP短标记(short_open_tag)','access'=>'需开启');
    if(ini_get('short_open_tag')) {
        $checkarr['currently'] = '<span class="font_3">√ 已开启</span>';
    } else {
        $next_enable = ' disabled';
        $checkarr['currently'] = '<span class="font_1">× 未开启</span>';
    }
    $accessevns[] = $checkarr;
    //附件上传
    $checkarr = array('name'=>'附件上传','access'=>'需开启');
    if(ini_get('file_uploads')) {
        $checkarr['currently'] = '<span class="font_3">√ 以开启</span>';
    } else {
        $next_enable = ' disabled';
        $checkarr['currently'] = '<span class="font_1">× 未开启</span>';
    }

    //文件目录属性监测
    include MUDDER_ROOT . 'install' . DS . 'accessfiles.php';
    foreach($accessfiles as $key => $access) {
        $checkfile = MUDDER_ROOT.$access['name'];
        if(!file_exists($checkfile)) {
            $next_enable = ' disabled';
            $access['currently'] = '<span class="font_1">× 不存在</span>';
        } elseif(!is_readable($checkfile) && $access['access'] == '可读') {
            $next_enable = ' disabled';
            $access['currently'] = '<span class="font_1">× 不可读</span>';
        } elseif(!is__writable($checkfile) && $access['access'] == '可写') {
            $next_enable = ' disabled';
            $access['currently'] = '<span class="font_1">× 不可写</span>';
        } else {
            if(is_readable($checkfile) && $access['access'] == 'read') {
                $access['currently'] = '<span class="font_3">√ 可读</span>';
            }
            if(is__writable($checkfile)) {
                $access['currently'] .= $access['currently'] ? ',可写' : '<span class="font_3">√ 可写</span>';
            }
        }
        $accessfiles[$key] = $access;
    }

} elseif($step == '2') {
    $cookiepre = random(5) . '_';
	$cookiedomain = '';
} elseif($step == '3') {
    $authkey = random(12);
    $siteurl = $_G['web']['url'] . MUDDER_URLPATH;
    if(!$_POST['system_exists_confirm']) {
        $dns = array();
        if(!$dns['dbhost'] = trim($_POST['dbhost']))    $error = '<li>未填写数据库服务器地址.</li>';
        if(!$dns['dbuser'] = trim($_POST['dbuser']))    $error .= '<li>未填写数据库名.</li>';
        $dns['dbpw'] = trim($_POST['dbpw']);
        if(!$dns['dbname'] = trim($_POST['dbname']))    $error .= '<li>未填写数据库用户名.</li>';
        if(!$dns['dbpre'] = trim($_POST['dbpre']))      $error .= '<li>未填写数据表前缀.</li>';
        if(!$cookiepre = trim($_POST['cookiepre']))     $error .= '<li>未输入Cookie前缀.</li>';
        if(strlen($cookiepre) < 3)                      $error .= '<li>Cookie前缀不能小于3个字符.</li>';
        if(strstr($dns['dbpre'], '.'))                  $error .= "<li>您指定的数据表前缀包含点字符，请返回修改.</li>";
		$cookiedomain = trim($_POST['cookiedomain']);

        $curr_os = PHP_OS;
        $curr_php_version = PHP_VERSION;
        if($curr_php_version < '4.4.0')                 $error .= "<li>您的PHP版本低于4.4.0, 无法安装使用 Modoer点评系统</li>";

        if(!$error) {
            $_G['db'] =& $_G['loader']->lib('database', NULL, TRUE, $dns);


            if($fp = fopen($configfile, 'r')) {
                $filecontent = fread($fp, filesize($configfile));
                fclose($fp);
            } else {
                echo '读取配置文件失败,请检查文件和目录权限.';
                exit();
            }

            $filecontent = preg_replace("/([$]_G\['dns'\]\['dbhost'\])\s*\=\s*[\"'].*?[\"']/is", "\\1 = '$dns[dbhost]'", $filecontent);
            $filecontent = preg_replace("/([$]_G\['dns'\]\['dbuser'\])\s*\=\s*[\"'].*?[\"']/is", "\\1 = '$dns[dbuser]'", $filecontent);
            $filecontent = preg_replace("/([$]_G\['dns'\]\['dbpw'\])\s*\=\s*[\"'].*?[\"']/is", "\\1 = '$dns[dbpw]'", $filecontent);
            $filecontent = preg_replace("/([$]_G\['dns'\]\['dbname'\])\s*\=\s*[\"'].*?[\"']/is", "\\1 = '$dns[dbname]'", $filecontent);
            $filecontent = preg_replace("/([$]_G\['dns'\]\['dbpre'\])\s*\=\s*[\"'].*?[\"']/is", "\\1 = '$dns[dbpre]'", $filecontent);
            $filecontent = preg_replace("/([$]_G\['cookiepre'\])\s*\=\s*[\"'].*?[\"']/is", "\\1 = '$cookiepre'", $filecontent);
            $filecontent = preg_replace("/([$]_G\['cookiedomain'\])\s*\=\s*[\"'].*?[\"']/is", "\\1 = '$cookiedomain'", $filecontent);

            if($fp = @fopen($configfile, 'w')) {
                @fwrite($fp, trim($filecontent));
                @fclose($fp);
            } else {
                echo '写入配置文件失败,请检查文件和目录权限.';
                exit();
            }

            $system_exists = FALSE;
            if($r = $_G['db']->query("SELECT COUNT(*) FROM {$dns[dbpre]}config", 'SILENT')) {
                $system_exists = TRUE;
            }

        }

        if($error) $step = 2;
    }
} elseif($step == '4') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password2 = trim($_POST['password2']);
    $authkey = trim($_POST['authkey']);
    $siteurl = trim($_POST['siteurl']);

    $error = install_check_username($username);
    $error .= install_check_email($email);
    $error .= install_check_password($password,$password2);
    $error .= install_check_authkey($authkey);

    if(empty($siteurl)) {
        $error .= '未填写 Modoer 所在路径.';
    }

    if($fp = fopen($sqlfile, 'rb'))  {
        $sql = fread($fp, 2048000);
        fclose($fp);
    } else {
        $error = '无法读取安装数据库文件。';
    }

    if($error) {
        $step = 3;
    } else {
        include $configfile;
        $_G['db'] =& $_G['loader']->lib('database', NULL, TRUE, $_G['dns']);

        $tablenum = 0;
        $create_text = '';

        $_G['loader']->helper('sql');
        sql_run_query($sql);

        unset($sql);

        $_G['db']->query("REPLACE INTO {$_G['dns']['dbpre']}config (variable,value,module) VALUES('authkey','$authkey','modoer')");
        $_G['db']->query("REPLACE INTO {$_G['dns']['dbpre']}config (variable,value,module) VALUES('siteurl','$siteurl','modoer')");
        $_G['db']->query("INSERT INTO {$_G['dns']['dbpre']}admin (adminname,email,password,admintype,is_founder,logintime,loginip,logincount,mymodules) VALUES ('$username','$email','".md5($password)."','1','Y','0','','0','')");

        $fp = fopen($lockfile, 'x+');
        fclose($fp);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET?>" />
<title>Modoer点评系统 - 安装程序</title>
<link href="./install/install.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="main">
<form method="post" action="<?php echo $php_self?>">
<h1 id="subject">
    <span id="copyright">Copyright &copy; 2007-2011 <a href="http://www.modoer.com/" target="_blank">Moufer Studio</a></span>
    <span id="title"><?php echo 'Modoer点评系统'.$_G['modoer']['version']?></span>
    <div class="clear"></div>
</h1>
<?php if($step == 1) { ?>
<div id="step">第一步:检测运行环境以及目录文件权限</div>
<?php if($error) {?><div id="error"><?php echo $error?></div><?php } ?>
<div>
运行环境：
<table>
    <tr id="tbth">
        <td width="60%">项目</td>
        <td width="20%" align="center">基本所需</td>
        <td width="20%" align="center">当前状态</td>
    </tr>
    <?php foreach($accessevns as $val) { ?>
    <tr><td><?php echo $val['name']?></td><td align="center"><?php echo $val['access']?></td><td align="center"><?php echo $val['currently']?></td></tr>
    <?php } ?>
</table>
文件目录权限：
<table>
    <tr id="tbth">
        <td width="60%">目录文件名称</td>
        <td width="20%" align="center">所需状态</td>
        <td width="20%" align="center">当前状态</td>
    </tr>
    <?php foreach($accessfiles as $accessfile) { ?>
    <tr><td><?php echo $accessfile['name']?></td><td align="center"><?php echo $accessfile['access']?></td><td align="center"><?php echo $accessfile['currently']?></td></tr>
    <?php } ?>
</table>
<div id="comment">如果您无法确认以上的配置信息，请与您的服务商联系。</div>
<input type="hidden" name="step" value="2" />
</div>
<?php } elseif($step == 2) { ?>
<div id="step">第二步:配置数据库和基本信息</div>
<?php if($error) {?><div id="error"><?php echo $error?></div><?php } ?>
<table>
    <tr id="tbth"><td width="150">设置项</td><td width="240">值</td><td width="*">说明</td></tr>
    <tr><td>服务器地址:</td><td><input type="text" name="dbhost" class="t_input" value="localhost" /></td><td>一般是 localhost(当前正在安装Modoer的服务器)</td></tr>
    <tr><td>数据库名:</td><td><input type="text" name="dbname" class="t_input" value="modoer" /></td><td>填写数据表安装所在数据库(需要已存在的库)</td></tr>
    <tr><td>数据库用户名:</td><td><input type="text" name="dbuser" class="t_input" value="root" /></td><td>可以连接数据库的帐号</td></tr>
    <tr><td>数据库用户密码:</td><td><input type="password" name="dbpw" class="t_input" /></td><td></td></tr>
    <tr><td>数据表前缀:</td><td><input type="text" name="dbpre" class="t_input" value="modoer_" /></td><td>同一数据库安装多个Modoer系统时，请更改</td></tr>
    <tr><td>Cookie前缀:</td><td><input type="text" name="cookiepre" class="t_input" value="<?php echo $cookiepre?>" /></td><td>任意3-8个字符, 不能和任何其他系统重复</td></tr>
	<tr><td>Cookie作用域:</td><td><input type="text" name="cookiedomain" class="t_input" value="<?php echo $cookiedomain?>" /></td><td>可填写网站顶级域名(如modoer.com)，亦可留空(默认)</td></tr>
</table>
<div id="comment">如果您无法确认以上的配置信息，请与您的服务商联系。</div>
<input type="hidden" name="step" value="3" />
<?php } elseif($step == 3) { ?>
<?php if($system_exists) { ?>
<div id="error">数据库中已经安装过 Modoer点评系统，继续安装会清空原有数据.<br />继续安装会清空全部原有数据，您确定要继续吗？</div>
<input type="hidden" name="system_exists_confirm" value="true" />
<input type="hidden" name="step" value="3" />
<?php } else { ?>
<div id="step">第三步:设置管理员账号</div>
<div id="msg">成功链接数据库.<br />您的服务器可以安装和使用 Modoer点评系统，请设置管理员帐号。</div>
<?php if($error) { ?><div id="error"><?php echo $error?></div><?php } ?>
<table>
    <tr id="tbth"><td width="150">设置项</td><td width="240">值</td><td width="*">说明</td></tr>
    <tr><td>创始人:</td><td><input type="text" name="username" class="t_input" value="admin" /></td><td>限2-15个字符</td></tr>
    <tr><td>电子邮件:</td><td><input type="text" name="email" class="t_input" value="admin@admin.com" /></td><td></td></tr>
    <tr><td>管理员密码:</td><td><input type="password" name="password" class="t_input" /></td><td>不宜过短</td></tr>
    <tr><td>确认密码:</td><td><input type="password" name="password2" class="t_input" /></td><td></td></tr>
    <tr><td>网站加密码:</td><td><input type="text" name="authkey" class="t_input" value="<?php echo $authkey?>" /></td><td>任意10个及以上字符</td></tr>
    <tr><td>Modoer所在路径:</td><td><input type="text" name="siteurl" class="t_input" value="<?php echo $siteurl?>" /></td><td>最后不要加“/”</td></tr>
</table>
<div id="comment">请认真确认以上信息，下一步将建立数据表，完成安装。</div>
<input type="hidden" name="step" value="4" />
<?php } ?>
<?php } elseif($step == 4) { ?>
<div id="step">第4步:创建数据表</div>
<div id="msg">安装程序已经顺利执行完毕，请尽快删除 <span class="font_1">install.php</span> 和 <span class="font_1">install</span> 目录，以免被他人恶意利用。<br />感谢您使用 Modoer点评系统。</div>
<div id="control">管理员帐号: <?php echo $username?><br />管理员密码: <?php echo $password?><br /><br /><a href="admin.php">后台管理</a><br /><a href="http://www.modoer.com/">Moufer Studio</a></div>
<?php } ?>
<?php if(is_numeric($step)) { ?>
<div id="next">
    <?php if($step >= 1 && $step <=3) { ?><button type="button" name="name" onclick="history.go(-1);">上一步</button>&nbsp;<?php } ?>
    <?php if($step >=1 && $step < 4) { ?><button type="submit" name="name"<?php echo $next_enable?>>下一步</button><?php } ?>
</div>
<?php } ?>
</form>
</div>
</body>
</html>
<?php
function install_redirect($msg,$url,$min) {

}

function install_check_username($username) {
    $error = '';
    if(!$username) {
        $error = '未填写用户名.<br />';
    } elseif(strlen($username) < 2 || strlen($username) > 16) {
        $error = '用户名不能小于2个字符或者大于16个字符.<br />';
    } elseif($username != addslashes($username)) {
        $error = '用户名包含了敏感字符.<br />';
    } else {
        $name_key = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#','$','(',')','%','@','+','?',';','^');
        foreach($name_key as $value){
            if (strpos($username, $value) !== false){ 
                $error = "<p>用户名包含了敏感字符.</p>";
            }
        }
    }
    return $error;
}

function install_check_email($email) {
    $error = '';
    if(!$email) {
        $error .= '未填写电子邮件地址.<br />';
    } elseif(strlen($email) < 6 || strlen($email) > 60) {
        $error .= '电子邮件不能小于6个字符或者大于60个字符.<br />';
    } elseif(!isemail($email)) {
        $error .= '电子邮件格式不正确.<br />';
    }
    return $error;
}

function install_check_password($password,$password2) {
    $error = '';
    if(!$password) {
        $error .= '未填写管理员密码.<br />';
    }
    if($password != stripslashes($password)) {
        $error .= '密码包含了敏感字符.<br />';
    }
    if($password != $password2) {
        $error .= '2次输入的密码不一致.<br />';
    }
    return $error;
}

function install_check_authkey($authkey) {
    $error = '';
    if(!$authkey) {
        $error .= '未输入网站加密码.<br />';
    } elseif(strlen($authkey) < 10) {
        $error .= '网站加密码不能小于10个字符.<br />';
    }
    return $error;
}

function get_gd_version($user_ver = 0)
{
    if (! extension_loaded('gd')) { return; }
    static $gd_ver = 0;
    // Just accept the specified setting if it's 1.
    if ($user_ver == 1) { $gd_ver = 1; return 1; }
    // Use the static variable if function was called previously.
    if ($user_ver !=2 && $gd_ver > 0 ) { return $gd_ver; }
    // Use the gd_info() function if possible.
    if (function_exists('gd_info')) {
        $ver_info = gd_info();
        preg_match('/\d/', $ver_info['GD Version'], $match);
        $gd_ver = $match[0];
        return $match[0];
    }
    // If phpinfo() is disabled use a specified / fail-safe choice...
    if (preg_match('/phpinfo/', ini_get('disable_functions'))) {
        if ($user_ver == 2) {
            $gd_ver = 2;
            return 2;
        } else {
            $gd_ver = 1;
            return 1;
        }
    }
    // ...otherwise use phpinfo().
    ob_start();
    phpinfo(8);
    $info = ob_get_contents();
    ob_end_clean();
    $info = stristr($info, 'gd version');
    preg_match('/\d/', $info, $match);
    $gd_ver = $match[0];
    return $match[0];
} // End gdVersion()

?>