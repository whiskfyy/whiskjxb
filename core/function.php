<?php
/**
* 通用函数库
* @author moufer<moufer@163.com>
* @copyright (C)2001-2007 Moufersoft
*/
!defined('IN_MUDDER') && exit('Access Denied');

define('MF_INT_KEY', '_int_keyid');
define('MF_INT', 'intval');
define('MF_FLOAT', 'floatval');
define('MF_HTML', '_HTML');
define('MF_TEXT', '_T');

//取得$_GET里的变量
function _get($var, $default = null, $convert_fun='') {
    if(isset($_GET[$var])) {
        if($convert_fun) return $convert_fun($_GET[$var]);
        return $_GET[$var];
    }
    return $default;
}
//取得$_GET里的变量
function _post($var, $default = null, $convert_fun='') {
    if(isset($_POST[$var])) {
        if($convert_fun) return $convert_fun($_POST[$var]);
        return $_POST[$var];
    }
    return $default;
}
//取得$_COOKIE里的变量
function _cookie($var, $default = null, $prefix = 1) {
    global $_G;
    if($prefix) {
        return isset($_G['cookie'][$var]) ? $_G['cookie'][$var] : $default;
    } else {
        return isset($_COOKIE[$var]) ? $_COOKIE[$var] : $default;
    }
}
//取得一个输入变量
function _input($var, $default = null, $convert_fun='', $sx='pg') {
    $r = $default;
    $c = strlen($sx);
    $funs = array('p'=>'_post','g'=>'_get');
    for($i=0;$i<$c;$i++) {
        $x = $sx{$i};
        $f = isset($funs[$x]) ? $funs[$x] : '';
        if(!$f) continue;
        $r = $f($var, $default, $convert_fun);
        if(!empty($r) && $r != $default) return $r;
    }
    if(!$r) $r = $default;
    return $r;
}
//设置$_POST里的变量
function set_post($var, $value = '') {
    $_POST[$var] = $value;
}
//设置$_POST里的变量
function set_get($var, $value = '') {
    $_GET[$var] = $value;
}
//设置cookie
function set_cookie($var, $value, $life = 0, $prefix = 1) {
    $life = $life ? _G('timestamp') + $life : 0;
    $secure = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
    $var = ($prefix ? _G('cookiepre') : '') . $var;
    return setcookie($var, $value, $life, _G('cookiepath'), _G('cookiedomain'), $secure);
}
//删除cookie
function del_cookie($var, $prefix = 1) {
    if(is_array($var)) {
        foreach($var as $val) set_cookie($val, '', -360000, $prefix);
    } else {
        set_cookie($var, '', -360000, $prefix);
    }
}
// Get Global value
function &_G() {
    global $_G;
    $max_level = 5;
    $result = '';
    $args_num = func_num_args();
    if($args_num > $max_level) return $result;
    $args = func_get_args();
    $val =& $_G;
    foreach ($args as $v) {
        if(!isset($val[$v])) return $result;
        $val =& $val[$v];
    }
    return $val;
}
//获取一个数字型的数据库主键id值
function _int_keyid($string) {
    return abs((int)$string);
}
//过滤HTML，用于Text
function _T($string) {
    if(is_array($string))
        foreach($string as $key => $val) $string[$key] = _T($val); 
    else
        $string = is_string($string) ? htmlspecialchars(trim($string), ENT_QUOTES) : $string;
    $wu = array('&amp;copy;','&amp;reg;','&amp;trade;');
    $rp = array('&copy;','&reg;','&trade;');
    return str_replace($wu, $rp, $string);
}
//过滤HTML，用于TextArea
function _TA($string) {
    return preg_replace("/(\r\n|\n\r|\n|\r)/", "\r\n", _T($string));
}
//支持HTML格式,过滤可能引起安全问题的HTML标记
function _HTML($string) {
    $search_arr = array("/(javascript|jscript|js|vbscript|vbs|about):/i","/on(mouse|exit|error|click|dblclick|key|load|unload|change|move|submit|reset|cut|copy|select|start|stop)/i","/<script([^>]*)>/i","/<iframe([^>]*)>/i","/<frame([^>]*)>/i","/<link([^>]*)>/i","/@import/i");
    $replace_arr = array("\\1\n:","on\n\\1","&lt;script\\1&gt;","&lt;iframe\\1&gt;","&lt;frame\\1&gt;","&lt;link\\1&gt;","@\nimport");
    $string = preg_replace($search_arr, $replace_arr, $string);
    $string = str_replace("&#", "&\n#", $string);
    return $string;
}
//格式化换行符号
function _NL($string) {
    return trim(preg_replace("/\s*(\r\n|\n\r|\n|\r)\s*/", "\r\n", $string));
}
//把字符串转化为JS的字符变量
function _JStr($string) {
	return str_replace(array('"', "\r\n", "\n"), array('\"', '', ''), $string);
}
//简单加密
function authcode($string, $operation = 'DECODE') {
    $string = $operation == 'DECODE' ? base64_decode($string) : base64_encode($string);
    return $string;
}
//数组格式化
function arrayeval($array, $level = 0) {

    if(!is_array($array)) {
        return "'".$array."'";
    }
    if(is_array($array) && function_exists('var_export')) {
        return var_export($array, true);
    }
    $space = '';
    for($i = 0; $i <= $level; $i++) {
        $space .= "\t";
    }
    $evaluate = "array (\n\r";
    $comma = $space;
    if(is_array($array)) {
        foreach($array as $key => $val) {
            $key = is_string($key) ? '\''.add_cs_lashes($key).'\'' : $key;
            $val = !is_array($val) && (!preg_match("/^\-?[0-9]\d*$/", $val) || strlen($val) > 12) ? '\''.add_cs_lashes($val, '\'\\').'\'' : $val;
            if(is_array($val)) {
                $evaluate .= "$comma$key => ".arrayeval($val, $level + 1);
            } else {
                $evaluate .= "$comma$key => $val";
            }
            $comma = ",\n\r$space";
        }
    }
    $evaluate .= "\n\r$space)";
    return $evaluate;
}
//以 C 语言风格使用反斜线转义字符串中的字符
function add_cs_lashes($string) {
    return $string ? addcslashes($string, '\'\\') : '';
}
//转换数组里的全部数值
function new_intval($number) {
    if(is_array($number))
        foreach($number as $key => $val) $number[$key] = new_intval($val); 
    else
        return intval($number);
    return $number;
}
//Un-quotes a quoted string
function strip_slashes($string) {
    if(is_array($string))
        foreach($string as $key => $val) $string[$key] = strip_slashes($val); 
    else
        $string = is_string($string) ? stripslashes($string) : $string;
    return $string;
}
//过滤SQL
function strip_sql($string) {
    $pattern_arr = array("/ union /i", "/ select /i", "/ update /i", "/ outfile /i", "/ or /i");
    $replace_arr = array('&nbsp;union&nbsp;', '&nbsp;select&nbsp;', '&nbsp;update&nbsp;','&nbsp;outfile&nbsp;', '&nbsp;or&nbsp;');
    return is_array($string) ? array_map('strip_sql', $string) : preg_replace($pattern_arr, $replace_arr, $string);
}
//过滤orderby
function strip_order($string) {
    $string = preg_replace('/.?select.+from.+/i', '', $string);
    $string = preg_replace("/.?delete.+from.+/i", '', $string);
    $string = preg_replace("/.?update.+set.+/i", '', $string);
    $string = preg_replace("/.?select.+union.+/i", '', $string);
    return $string;
}
//转换浮点数
function cfloat($float) {
    $num = (float)$float;
    return $num;
}
//从开端截取
function trimmed_title($text, $limit=12, $ext='') {
    if ($limit) {
        $val = csubstr($text, 0, $limit);
        return $val[1] ? $val[0].$ext : $val[0];
    } else {
        return $text;
    }
}
//截取
function csubstr($text, $start=0, $limit=12) {
    $charset = _G('charset');
    if (function_exists('mb_substr')) {
        $more = (mb_strlen($text, $charset) > $limit) ? true : false;
        $text = mb_substr($text, 0, $limit, $charset);
        return array($text, $more);
    } elseif (function_exists('iconv_substr')) {
        $more = (iconv_strlen($text) > $limit) ? true : false;
        $text = iconv_substr($text, 0, $limit, $charset);
        return array($text, $more);
    } elseif (strtolower($charset) == "utf-8") {
        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $text, $ar);
        if(func_num_args() >= 3) {
            if (count($ar[0])>$limit) {
                $more = true;
                $text = join("",array_slice($ar[0],0,$limit))."...";
            } else {
                $more = false;
                $text = join("",array_slice($ar[0],0,$limit));
            }
        } else {
            $more = false;
            $text = join("",array_slice($ar[0],0));
        }
        return array($text, $more);
    } else {
        $fStart = 0;
        $fStart = $fStart * 2; 
        $limit = $limit * 2; 
        $strlen = strlen($text);
        for ( $i = 0; $i < $strlen; $i++ ) { 
            if ($i >= $fStart && $i < ($fStart + $limit ) ) { 
                if (ord(substr($text, $i, 1)) > 129) $tmpstr .= substr($text, $i, 2); 
                else $tmpstr .= substr($text, $i, 1); 
            } 
            if (ord(substr($text, $i, 1)) > 129 ) $i++; 
        } 
        $more = strlen($tmpstr) < $strlen; 
        return array($tmpstr, $more);
    }
}
//计算字符数量，非占用字节
function strlen_ex($str) {
    $charset = _G('charset');
    if(function_exists('mb_strlen')) {
        return mb_strlen($str, $charset);
    } elseif(function_exists('iconv_strlen')) {
        return iconv_strlen($str, $charset);
    } elseif($charset == 'utf-8') {
        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $str, $ar);
        return count($ar[0]);
    } else {
        $len = 0;
        $strlen = strlen($str);
        for( $i = 0; $i < $strlen; $i++ ) { 
            $len++;
            if (ord(substr($str, $i, 1)) > 129 ) $i++; 
        }
        return $len;
    }
}
//转换unix时间戳
function newdate($date, $format='Y-m-d H:i', $dnum=2, $dunit='月') {
    $timestamp = _G('timestamp');
    $date == 'NOW' ? $timestamp : $date;
    if(!$date) return '';
    $date = is_numeric($date) ? $date : (!$date ? $timestamp : strtotime($date));
    $date = ($date == -1 || !$date) ? $timestamp : $date;
    if($format != 'w2style') {
        return date($format, $date);
    } else {
        $tm = $timestamp - $date;
        $num = 0;
        if($tm < 60) {
            $num = $tm;
            $unit = 'second';
        } elseif($tm < 3600) {
            $num = floor($tm / 60);
            $unit = 'minute';
        } elseif($tm < 3600 * 24) {
            $num = floor($tm / 3600);
            $unit = 'hour';
        } elseif($tm < 3600 * 24 * 30) {
            $num = floor($tm / (3600 * 24));
            $unit = 'day';
        } elseif($tm < 3600 * 24 * 30 * 365) {
            $num = floor($tm / (3600 * 24 * 30));
            $unit = 'month';
        }
        if($dnum <= $num && $dunit == $unit) {
            return date('Y-m-d H:i', $date);
        }
        return $num > 0 ? (lang('global_time_format', array($num, lang('global_time_'.$unit)))) : date('Y-m-d', $date);
    }
}
//判断电子邮件
function isemail($email) {
    return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}
//判断文件是否可写
function is__writable($path) {
    if ($path{strlen($path)-1}=='/')
        return is__writable($path.uniqid(mt_rand()).'.tmp');
    else if (is_dir($path))
        return is__writable($path.'/'.uniqid(mt_rand()).'.tmp');
    $rm = file_exists($path);
    $f = @fopen($path, 'a');
    if ($f===false)
        return false;
    fclose($f);
    if (!$rm)
        unlink($path);
    return true;
}
//获取随机数 ALL(数字或字母),NUM(数字),WORD(字母)
function random($length=8, $idtype='ALL') {
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    $hash = '';
    for ($i = 0; $i < $length;  $i++) {
        if ( 'NUM' == $idtype ) {
            if (0==$i) {
                $hash .= chr(rand(49, 57));
            } else {
                $hash .= chr(rand(48, 57));
            }
        } else if ( 'WORD' == $idtype ){
            $hash .= chr(rand(65, 90));
        } else {
            if ( 0==$i ) {
                $hash .= chr(rand(65, 90));
            } else {
                $hash .= (0==rand(0,1))?chr(rand(65, 90)):chr(rand(48,57));
            }
        }
    }
    return $hash;
}
//生成参数序列
function create_identifier($params) {
    return substr(md5(serialize($params)),0,8);
}
//生成表单序列
function create_formhash($p1, $p2, $p3) {
    $authkey = _G('cfg','authkey');
    return substr(md5($authkey . $p1 . $p2 . $p3), 8, 8);
}
//替换全角数字
function cdc2dbc($number) {
    $search_arr = array('０','１','２','３','４','５','６','７','８','９');
    $replace_arr = array('0','1','2','3','4','5','6','7','8','9');
    return str_replace($search_arr, $replace_arr, $number);
}
//判断字串长度范围
function string_length($string, $min, $max) {
    return strlen($string) >= $min && strlen($string) <= $max;
}
/**
 * 判断是否为0 ，支持浮点数
 * @return Boolean
 * */
function num_empty($num, $alow_minus = TRUE) {
    if(!$alow_minus) {
        $num = (int) $num;
        return $num == 0;
    }
    if($num > 0 || $num < 0) return false;
    return true;
}
/**
 * 在字符串中查找指定的字符
 * @return Boolean
 * */
function strposex($string, $find, $offset = 0) {
    return !(strpos($string, $find, $offset) === false);
}
//获取大小单位
function size_unit($filesize) {
	if($filesize >= 1073741824) {
		$filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
	} elseif($filesize >= 1048576) {
		$filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
	} elseif($filesize >= 1024) {
		$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
	} else {
		$filesize = $filesize . ' bytes';
	}
	return $filesize;
}
//取得字节单位
function size_bytes($val) {
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last) {
        case 'g':
             $val *= 1024;
        case 'm':
             $val *= 1024;
        case 'k':
             $val *= 1024;
    }
    return $val;
}
//写入文本内容
if(!function_exists('file_get_contents')) {
    function file_get_contents($file) {
        if(!@$fp = fopen($file, 'r')) {
            show_error("Could not open file " . (DEBUG ? "($file)" : "") . "!");
        }
        $content = fread($fp, filesize($file));
        fclose($fp);
        return $content;
    }
}
//获取文本内容
if(!function_exists('file_put_contents')) {
    function file_put_contents($file, $content) {
        if(!@$fp = fopen($file, 'w')) {
            show_error("The specified file " . (DEBUG ? "($file)" : "") . "may not exist!");
        }
        flock($fp, 2);
        fwrite($fp, $content, strlen($content));
        fclose($fp);
        return TRUE;
    }
}
//分页
function multi($num, $perpage, $curpage, $mpurl, $anchor = '', $onclick = '') {
    $multipage = '';
    if(substr($mpurl,0,11) != 'javascript:') {
        $mpurl = preg_replace("/page=(.*?)(&|$)/i","page=_PAGE_\\2",$mpurl);
        if(!strpos($mpurl, '_PAGE_')) {
            $mpurl .= strposex($mpurl, '?') ? '&amp;' : '?';
            $mpurl = $mpurl . 'page={PAGE}' . $anchor;
        } else {
            $mpurl = str_replace('_PAGE_', '{PAGE}', $mpurl) . $anchor;
        }
    }
    if($onclick) {
        $onclick = ' onclick="return ' . $onclick . '";';
    }
    if($num > $perpage) {
        $page = 10;
        $offset = 5;
        $pages = @ceil($num / $perpage);
        if($page > $pages) {
            $from = 1;
            $to = $pages;
        } else {
            $from = $curpage - $offset;
            $to = $curpage + $page - $offset - 1;
            if($from < 1) {
                $to = $curpage + 1 - $from;
                $from = 1;
                if(($to - $from) < $page && ($to - $from) < $pages) {
                    $to = $page;
                }
            } elseif($to > $pages) {
                $from = $curpage - $pages + $to;
                $to = $pages;
                if(($to - $from) < $page && ($to - $from) < $pages) {
                    $from = $pages - $page + 1;
                }
            }
        }
        $multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.str_replace('{PAGE}',1,$mpurl).'"'.str_replace('{PAGE}',1,$onclick).' class="p_redirect">&lt;&lt;</a>' : '').($curpage > 1 ? '<a href="'.str_replace('{PAGE}',$curpage-1,$mpurl).'"'.str_replace('{PAGE}',$curpage-1,$onclick).' class="p_redirect">&lt;</a>' : '');
        for($i = $from; $i <= $to; $i++) {
            $multipage .= $i == $curpage ? '<span class="p_curpage">'.$i.'</span>' : '<a href="'.str_replace('{PAGE}',$i,$mpurl).'"'.str_replace('{PAGE}',$i,$onclick).' class="p_num">'.$i.'</a>';
        }
        $multipage .= ($curpage < $pages ? '<a href="'.str_replace('{PAGE}',$curpage+1,$mpurl).'"'.str_replace('{PAGE}',$curpage+1,$onclick).' class="p_redirect">&gt;</a>' : '').($to < $pages ? '<a href="'.str_replace('{PAGE}',$pages,$mpurl).'"'.str_replace('{PAGE}',$pages,$onclick).' class="p_redirect">&gt;&gt;</a>' : '');
        $multipage = $multipage ? '<div class="p_bar"><span class="p_info">'.$num.'</span>'.$multipage.'</div>' : '';
    }
    return $multipage;
}
//分页2
function multi_w($count, $offset, $page, $mpurl, $anchor = '') {
    $multipage = '';
    $mpurl .= strpos($mpurl, '?') ? '&amp;' : '?';

    $start = ($page-1) * $offset;
    if($start > 1) $multipage .= '<a href="'.$mpurl.'page='.($page-1).'" class="p_redirect">&lt;</a>';
    if($count > 0) $multipage .=  '<span class="p_info">' . ($start + 1) . '~' . ($start + $count) . '</span>';
    if($count >= $offset) $multipage .= '<a href="'.$mpurl.'page='.($page + 1).'" class="p_redirect">&gt;</a>';
    $multipage = $multipage ? '<div class="p_bar">'.$multipage.'</div>' : '';
    return $multipage;
}
//检测模块是否存在
function check_module($flag) {
    $module =& _G('modules',$flag);
    return $module && is_array($module);
}
//检测提交是否有效
function check_submit($var, $allowget = 0) {
    if(empty($_POST[$var])) return FALSE;
    if($allowget || ($_SERVER['REQUEST_METHOD'] == 'POST' && (empty($_SERVER['HTTP_REFERER']) || 
        preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])))) {
        return TRUE;
    }
    redirect('global_op_submit_invalid');
}
//检测验证码
function check_seccode($seccode, $return = FALSE, $del=true) {
    if(!$seccode) redirect('global_op_seccode_empty');
    if(!$cookie_seccode = _G('cookie', 'seccode')) {
        if($return) return FALSE;
        redirect('global_op_seccode_cookie_empty');
    }
    $hash = create_formhash(strtolower($seccode),'','');
    if($hash != $cookie_seccode) {
        if($return) return FALSE;
        redirect('global_op_seccode_invalid');
    }
    if($del) del_cookie('seccode');
    return TRUE;
}
//检测IP
function check_ipaccess($ipstr) {
    $myip = _G('ip');
    if(!$ipstr) return false;
    $iplist = explode("\r\n", $ipstr);
    return in_array($myip, $iplist);
}
//消息提示
function redirect($lang, $url = 'javascript:history.go(-1);', $navs = null, $min = '3') {
    global $_G;
    if(preg_match('/^[a-zA-Z0-9_\-]+$/', $lang)) {
        $msg = lang($lang);
    } else {
        $msg = $lang;
    }

    if($lang == 'member_not_login') {
        $extra = 'login';
    } elseif(strpos($lang, '_succeed') === false) {
        $extra = 'error';
    } else {
        $extra = '';
    }

    $url = str_replace('&amp;','&',$url);

    if(defined('IN_AJAX')) {
        dialog(lang('global_op_title'), $msg, $extra);
    } elseif(defined("IN_ADMIN")) {
        cpmsg($msg, $url);
    } else {
        $auto_url = trim($url);
        if($auto_url == 'stop'  || !$auto_url) $auto_url = '';
        if(!is_array($navs)) $navs = array();
        array_unshift($navs,array(lang('global_window_close'),'javascript:window.close();'));
        if(!$_G['user']->isLogin) {
            array_unshift($navs,array(lang('member_login_title'),url('member/login')));
        } else {
            array_unshift($navs,array(lang('member_operation_title'),url('member/index')));
        }
        array_unshift($navs,array(lang('global_index'),url('modoer/index')));
        require_once template('modoer_redirect');
        output();
    }
}
//ajax消息提示
function dialog($caption, $msg, $extra = '') {
    $caption = _T($caption);
    $msg = trim($msg);
    $search = array('"',"\r\n","\r","\n","\n\r");
    $replace = array('\\"',"{LF}","{LF}","{LF}","{LF}");
    if($extra == 'error' || $extra == 'login') {
        $msg = 'ERROR:' . $msg;
    }
    echo '{ caption:"' . $caption . '",message:' . '"'.str_replace($search, $replace, $msg) . '"';
    if($extra == 'login') {
        $forward = _G('web','referer') ? _G('web','referer') : _G('cfg','siteurl');
        $url = get_url('member', 'login', array('forward'=>base64_encode($forward)), '', 1);
        echo ',extra:"'.$extra.'",url:"'.$url.'" }';
    } elseif($extra == 'dlg') {
        echo ',extra:"dlg" }';
    } elseif($extra == 'msg') {
        echo ',extra:"msg" }';
    } else {
        echo '}';
    }
    output();
}
//页面不存在
function http_404($nexturl='') {
    global $_G, $_CFG;
    if($nexturl) {
        $nexturl = str_replace('&amp;','&',$nexturl);
    } else {
        $nexturl = url('modoer/index');
    }
    $msg = lang('global_http_404');
    @header('HTTP/1.1 404 Not Found');
    @header('Status: 404 Not Found');
    require_once template('modoer_http_404');
    output();
}
//数组转换url
function http_query($array, $pathinfo=FALSE, $check_rewritecompatible=TRUE) {
    global $_G;
    if($check_rewritecompatible) {
        foreach($array as $key => $val) {
            if(is_string($val)) {
                $v = rawurlencode($val);
                if($_G['cfg']['rewrite'] && $_G['cfg']['rewrite_hide_index'] && $_G['cfg']['rewritecompatible']) {
                    $v = rawurlencode($v);
                }
                $array[$key] = $v;
            }
        }
    }
    $param = url_implode($array,null,'','',false);
    if($pathinfo) {
        return str_replace(array('&','='), '/', $param);
    }
    return $param;
}
//转换模板url标签
function url($pathinfo, $au='', $fullurl=FALSE, $no_rewrite=FALSE) {

    if(strtolower(substr($pathinfo,0,4)) == 'http') {
        return $pathinfo;
    }
    $params = array();
    $info = explode("/", $pathinfo);
    if(empty($info)) {
        $module = "modoer";
        $script = "";
    } elseif(count($info) == 1) {
        $module = $info[0];
        $script = "";
    } else {
        $module = $info[0];
        $script = $info[1];
        if(count($info) > 2) {
            for($i=2; $i<count($info); $i++) {
                $tmp = $info[++$i];
                if($tmp == '') continue;
                $params[$info[$i-1]] = $tmp; 
            }
        }
    }
    return get_url($module, $script, $params, $au, $fullurl, $no_rewrite);
}
//URL路径
function url_path($name, $url='', $target='') {
    if($target) $target = ' target="'.$target."'";
    if($url)
        return '<a href="'.$url.'"'.$target.'>'.$name.'</a>';
    else
        return $name;
}
//转换数组成为URL
function url_implode($data, $prefix=null, $sep='', $key='', $encode=true) {
    $ret = array();
    foreach((array)$data as $k => $v) {
       $k = urlencode($k);
       if(is_int($k) && $prefix != null) {
           $k = $prefix . $k;
       }
       if(!empty($key)) {
           $k = $key . "[" . $k . "]";
       }
       if(is_array($v) || is_object($v)) {
           array_push($ret,url_implode($v, "", $sep, $k));
       }
       else {
           array_push($ret, $k . "=" . ($encode?rawurlencode($v):$v));
       }
    }
   if(empty($sep)) $sep = "&amp;";
   return implode($sep, $ret);
}
//替换URL的其中一个参数的值
function url_replace($url, $name, $value, $del_page = TRUE) {
    $value = rawurlencode($value);
    if(strposex($url, $name)){
        $url = preg_replace("/$name=(.*?)(&|$)/i","$name=$value\\2", $url);
    } else {
        $url .= (strpos($url,'?') ? "&" : "?") . "$name=$value";
    }
    if(!$del_page) return $url;
    return preg_replace("/(\?|&amp;&|)a=(.*?)(&|$)/i","\\2", $url);
}
//获取服务器信息
function get_webinfo() {
    $result = array();
    $result['self'] = strtolower($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
    $result['domain'] = strtolower($_SERVER['SERVER_NAME']);
    $result['agent'] = $_SERVER['HTTP_USER_AGENT'];
    $result['referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    $result['scheme'] = $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $result['reuri'] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $result['port'] = $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT'];
    $result['url'] = $result['scheme'] . $result['domain'] . $result['port'];
    return $result;
}
//获取主域名
function get_current_domain() {
    $ServerName = strtolower($_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']);
    if($i=strpos($ServerName,':')) $ServerName = substr($ServerName,0,$i);
    return str_replace(array('http://','https://'),'',$ServerName);
}
//获取顶级域名
function get_fl_domain() {
	$url = get_current_domain();
	preg_match('/[\w][\w-]*\.(?:com\.cn|net\.cn|org\.cn|gov\.cn|com\.hk|com\.tw|idv\.tw|org\.tw|ne\.jp|co\.nz|com|net|org|biz|info|cn|hk|mobi|name|sh|ac|la|travel|tm|us|cc|tv|jobs|asia|hn|lc|hk|bz|ws|tel|cm|tw|io|tel|ag|in|mn|sc|vc|ws|ca|me|jp|nz|cd)(\/|$)/isU', $url, $domain);
	return rtrim($domain[0], '/');
}
//获取二级域名前缀
function get_sl_domain() {
    $domain = get_current_domain();
	$list = explode('.', $domain);
	if(count($list)<=2 || $list[0]=='www') return '';
	return $list[0];
}
//获取头像链接
function get_face($uid, $create_src = FALSE, $show_root = TRUE) {
    if(defined('IN_UC')) {
        return UC_API.'/avatar.php?uid='.$uid.'&type=virtual&size=small';
    } else {
        $uid = abs(intval($uid));
        $uid = sprintf("%09d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);
        $root = url('modoer', '', TRUE);
        $src = 'uploads/faces/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2) . ".jpg";
        if($create_src || is_file(MUDDER_ROOT . $src)) return ($show_root ? $root : URLROOT) . $src;
        return ($show_root ? $root : URLROOT) . 'static/images/noface.jpg';
    }
}
//转换页码为数据开始下标
function get_start($page, $offset) {
    if(!$offset || $offset < 1) $offset = 10;
    return ($page-1) * $offset;
}
//转换输出URL
function get_url($module='modoer', $scriptname='', $param='', $au='', $full = FALSE, $no_rewrite = FALSE) {
    global $_G;

    $_modules =& _G('modules');

    if(defined('IN_AJAX')) $full = TRUE;
    if(isset($_G['fullalways']) && $_G['fullalways']) $full = TRUE;

    static $instance = array();
    if(!defined("IN_ADMIN") && !empty($_G['mod'])) {
        $cmod =& _G('mod');
        //$current_empty_dir = empty($cmod['directory']);
        $current_empty_dir = TRUE;
    }

    $siteurl = _G('cfg', 'siteurl');

    $urlpre = $full ? $siteurl : URLROOT . '/';

    if($module != 'modoer' || !$module) {
        if(!$param) $param = array();
        if($scriptname && is_array($param)) {
            array_unshift($param, array('act' => $scriptname));
        } elseif($scriptname && is_string($param)) {
            $param = 'act=' . $module . ($param ? ('&' . $param) : '');
        }
        $scriptname = $module;
    }

    $scriptname && $scriptname .= '.php';
    if(is_array($param) && $param) {
        if(!$no_rewrite && _G('cfg','rewrite')) {
            foreach($param as $k=>$v) $param[$k] = str_replace('-', '_f_',$v);
        }
        $paramstr = $scriptname . '?' . http_query($param,0,!$no_rewrite);
    } elseif(is_string($param) && $param) {
        $paramstr = $scriptname .'?' . $param;
    } else {
        $paramstr = $scriptname;
    }

    if(!$no_rewrite && _G('cfg','rewrite') && $paramstr) {
        $RW =& $_G['loader']->lib('urlrewriter');
        $paramstr = $RW->preg_parse($paramstr);
    }
    $austr = '';
    $au && $austr = '#' . $au;
    return $urlpre . $paramstr . $austr;
}
//获取IP
function get_ip() {
    $onlineip = null;
    $onlineipmatches = array();
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $onlineip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $onlineip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] &&
        strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $onlineip = $_SERVER['REMOTE_ADDR'];
    }
    $onlineip = addslashes($onlineip);
    @preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
    $onlineip = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
    unset($onlineipmatches);
    return $onlineip;
}
//获取根路径
function get_urlroot() {
    global $_G;
    $url = str_replace('\\', '/', dirname(SELF));
    if(strposex($url, '/index.php')) {
        $x = strpos($url, '/index.php');
        $url = substr($url, 0, $x);
        $_G['index_url_rewrite'] = TRUE;
    }
    if($url == '/' || $url == '\\') 
        $url = '';
    return $url;
}
//获取上一页URL，并设置没有时的默认返回
function get_forward($default = null, $get_post = FALSE) {
    $web = _G('web');
    $siteurl = _G('siteurl');
    if($get_post) {
        $forward = $_POST['forward'];
    } else {
        $forward = $web['referer'] ? $web['referer'] : '';
    }
    if($forward && substr($forward, 0, strlen($siteurl)) != $siteurl) $forward = '';
    if(!$forward && $default) {
        return $default == 'home' ? (defined('IN_ADMIN') ? SELF : url('modoer/index')) : $default;
    }
    return _T($forward);
}
//载入模板
function template($file, $type='main', $templateid='', $custom_dir='', $update=FALSE) {
    global $_G, $_CFG;

    if(!$templateid) {
        $mainid = $_G['cfg']['templateid'] ? $_G['cfg']['templateid'] : 1;
    } else {
        $mainid = $templateid;
    }
    $templates = $_G['loader']->variable('templates');
    $main_dir = isset($templates['main'][$mainid]) ? $templates['main'][$mainid]['directory'] : 'default';
    $_G['tplurl'] = 'templates/main/'. $main_dir . '/';

    if($type == 'member') {
        $dir = 'core' . DS . 'modules' . DS . $templateid;
        $tpldir = $dir . DS . 'assistant' . DS . 'templates' . DS;
        $cachedir = 'data' . DS . 'templates' . DS;
        $_G['tplurl_member'] = 'templates/main/'. str_replace(DS,'/', $dir);
    } elseif($type == 'datacall') {
        $tpldir = 'templates' . DS . 'main' . DS . $main_dir . DS . 'datacall' . DS;
        $cachedir = 'data' . DS . 'templates' . DS;
        $_G['tplurl_datacall'] = $_G['tplurl'] . 'datacall/';
    } elseif($type != 'main') {
        $type_dir = isset($templates[$type][$templateid]) ? $templates[$type][$templateid]['directory'] : 'default';
        $tpldir = 'templates' . DS . $type . DS . $type_dir . DS;
        $_G['tplurl_'.$type] = 'templates/'.$type.'/'. str_replace(DS,'/', $type_dir) . '/';
    } else {
        if($custom_dir) {
            $tpldir = $custom_dir . DS;
            $_G['tplurl_custom'] = str_replace(DS, '/', $custom_dir) . '/';
            $templateid = $mainid . '_custom';
        } else {
            $tpldir = 'templates' . DS . $type . DS . $main_dir . DS;
            $_G['tplurl_'.$type] = 'templates/' . $type . '/' . $main_dir . '/';
            $templateid = $mainid . '_' . $type;
        }
    }

    $cachedir = 'data' . DS . 'templates' . DS;
    $ext = empty($_G['cfg']['tplext']) ? '.htm' : $_G['cfg']['tplext'];
    $tplfile = $tpldir . $file . $ext;
    $objfile = $cachedir . $templateid . '_' . $type . '_' . $file . '.tpl.php';

    if($update || !is_file(MUDDER_ROOT . $tplfile) || (@filemtime(MUDDER_ROOT . $tplfile) > @filemtime(MUDDER_ROOT . $objfile))) {
        $_G['loader']->helper('template');
        parse_template($tplfile, $objfile);
    }
    return MUDDER_ROOT . $objfile;
}
//模板尾部
function footer($templateid = '') {
    global $_G, $_CFG;

    if(!$templateid) {
        $mainid = $_G['cfg']['templateid'] ? $_G['cfg']['templateid'] : 1;
    } else {
        $mainid = $templateid;
    }

    $_G['fullalways'] = 1;
    if($_G['cfg']['scriptinfo']) {
        $mtime = explode(' ', microtime());
        $totaltime = number_format(($mtime[1] + $mtime[0] - $_G['starttime']), 6);
        $gzip = $_G['cfg']['gzipcompress'] ? 'enabled' : 'disabled';
        $sitedebug = 'Processed in '.$totaltime.' second(s), '.$_G['db']->query_num.' queries, Gzip '.$gzip;
    }
    if(!defined('IN_AJAX')) {
        require template('modoer_footer', 'main', $mainid);
    }
    output();
}
//程序结束ob处理
function output() {
    global $_G;
    $output = ob_get_contents();
    ob_end_clean();
    $_G['cfg']['gzipcompress'] ? @ob_start('ob_gzhandler') : ob_start();
    echo $output;
    exit;
}
//取得语言
function lang($lng, $param = null) {
    global $_G;
    $string = '';
    list($file, $page, ) = explode("_", $lng);
    if(!preg_match("/^[a-zA-Z_\.]+$/", $file)) {
        $string = $lng;
    } elseif(!isset($_G['lng'][$file])) {
        $filename = MUDDER_CORE . 'lang' . DS . $_G['lang_directory'] . DS . $file . '.php';
        if(!is_file($filename)) {
            $string = $lng;
        } else {
			$_G['lng'][$file] = include $filename;
		}
    }
    if(!$string && !isset($_G['lng'][$file][$lng])) {
        $string = $lng;
    } elseif(!$string) {
		$string = $_G['lng'][$file][$lng];
	}
	if($param) {
		if(!is_array($param)) $param = array($param);
		$string = vsprintf($string, $param);
	}
    return $string;
}
//跳转
function location($url) {
    if(!$url) return;
	$url = str_replace('&amp;', '&', $url);
    header('Location:' . $url);
    exit;
}
//终端程序，抛出错误信息
function show_error($lang, $param=null) {
    if(preg_match('/^[a-zA-Z0-9_\-]+$/', $lang)) {
        $message = lang($lang);
    } else {
        $message = $lang;
    }
	//ob_end_clean();
    echo $message;
    exit;
}
//中断程序，输出信息
function diex($msg) {
    global $_CFG;
    include MUDDER_CORE . 'diex.php';
    exit(0);
}
//格式化显示数组
function vp($var) {
    echo "<div style=\"border:1px solid #ddd;background:#F7F7F7;padding:5px 10px;\">\r\n";
    echo "<pre style=\"font-family:Arial,Vrinda;font-size:14px;\">\r\n";
    var_dump($var);
    echo "\r\n</pre>\r\n";
    echo "</div>";
}
//格式化显示数组
function pr($var) {
    echo "<div style=\"border:1px solid #ddd;background:#F7F7F7;padding:5px 10px;\">\r\n";
    echo "<pre style=\"font-family:Arial,Vrinda;font-size:14px;\">\r\n";
    print_r($var);
    echo "\r\n</pre>\r\n";
    echo "</div>";
}
//获取系统需要的文件修改时间
function myfilemtime($filename) {
    $timezone = _G('timezone');
    return filemtime($filename) + $_G['timezone'] * 3600;
}
//获取一个数字
function get_numeric($num) {
    $len = strlen($num);
    $sub = 0;
    for($i=$len-1;$i>=0;$i--) {
        if($num{$i} == '0') $sub++; else break;
    }
    if($sub>0) {
        $num = substr($num, 0, -$sub);
        if(substr($num,-1)=='.') $num = substr($num, 0, -1);
    }
    return $num;
}
//把秒转换成时间
function get_time($time) {
    $h = floor($time / 3600);
    $i = floor(($time  - 3600 * $h) / 60);
    $s = $time  - 3600 * $h - 60 * $i;
    return array($h, $i, $s);
}
//函数调用的默认参数处理
function query_default($params) {
    if(!isset($params['start']) || (!$params['start'] || $params['start'] < 0)) {
        $params['start'] = 0;
    }
    if(isset($params['row'])) $params['rows'] = $params['row'];
    if(!isset($params['rows']) || (!$params['rows'] || $params['rows'] < 0)) {
        $params['rows'] = 10;
    }
    if(!isset($params['cachetime']) || (!$params['cachetime'] || $params['cachetime'] < 0)) {
        $params['cachetime'] = 0;
    }
    if(!isset($params['ordersc']) || strtoupper($params['ordersc'])!='DESC') {
        $params['ordersc'] = 'ASC';
    }
    return $params;
}
//载入helper/print的函数(仅供模板引擎使用)
function template_print($flag,$fun,$params) {
    global $_G;
    if(!$fun) return '';
    $module = '';
    if($flag && $flag != 'modoer') {
        $modules =& $_G['modules'];
        if(!isset($modules[$flag])) return '';
        $module = $flag;
    }
    $_G['loader']->helper('display', $module);
    $classname = !$module ? 'display' : ('display_' . $module);
    $result = call_user_func(array($classname, $fun), $params);
    return $result;
}
//载入helper/print的函数(PHP内部使用)
function display($flag, $params) {
	global $_G;
	list($module, $fun) = explode(':', $flag);
	$modules =& $_G['modules'];
	if($module == 'modoer') $module = '';
	if($module) if(!isset($_G['modules'][$module])) return '';
	$_G['loader']->helper('display', $module);
	$classname = !$module ? 'display' : ('display_' . $module);
	$result = call_user_func(array($classname, $fun), params2array($params));
	return $result;
}
//获取星星个数
function get_star($point, $scoretype) {
    if($scoretype==5) return round($point);
    if($scoretype==10) return round($point/2);
    if($scoretype==100) return round($point/20);
}
//获取a/b形式到数组array
function params2array($str) {
	$info = explode('/', $str);
	if(count($info)<2) return null;
	$result = array();
    for($i=0; $i<count($info); $i++) {
        $tmp = $info[++$i];
        if($tmp == '') continue;
        $result[$info[$i-1]]= $tmp;
    }
	return $result;
}
?>