<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

// check cache file
function check_cache($cachefile, $life = -1) {
    if(is_file($cachefile)) {
        $time = filemtime($cachefile) + _G('timezone') * 3600;
        return $life < 0 || _G('timestamp') - $time < $life;
    } else {
        return false;
    }
}

function read_cache($cachefile, $mode = 'i') {
    if(!is_file($cachefile)) return false;
    return $mode == 'i' ? @include $cachefile : @file_get_contents($cachefile);
}

function write_cache($name, &$data, $model=NULL, $type='return', $custom_dir = '') {
    $cachedir = $custom_dir ? MUDDER_ROOT . ($custom_dir.DS) : MUDDER_CACHE;
    if(!$model) $model = 'modoer';
    $filename = $type == 'js' ? ($name . '.js') : ($model . '_' . $name . '.php');

    if(!is_dir($cachedir)) {
        if(@mkdir($cachedir, 0777)) {
            show_error(sprintf(lang('global_mkdir_no_access'), str_replace(MUDDER_ROOT,'./',$cachedir)));
        }
    }

    if($type=='js') {
        $content = "//Modoer cache file\r\n//Created on ".date('Y-m-d H:i:s', _G('timestamp')) . "\r\n\r\n" . $data . "\r\n";
    } elseif($type == 'return') {
        $content = "<?php\r\n!defined('IN_MUDDER') && exit('Access Denied');\r\nreturn " . $data . "; \r\n?>";
    } else {
        $content = "<?php\r\n//Modoer cache file\r\n//Created on ".date('Y-m-d H:i:s', _G('timestamp'))."\r\n\r\n!defined('IN_MUDDER') && exit('Access Denied');\r\n\r\n" . $data . "\r\n\r\n?>";
    }

    $file = $cachedir . $filename;
    if(!$x = file_put_contents($file, $content)) {
        show_error(lang('global_file_not_exist', str_replace(MUDDER_ROOT, '.'.DS, $file)));
    }
    @chmod($file, 0777);
}

function write_cache2($cachename, $cachedata = '', $extra = '', $mod='', $cachedir = '') {
    global $_G;

    $cachedir = $cachedir ? MUDDER_ROOT . $cachedir : MUDDER_CACHE;
    if(!$extra) {
        $filename = 'cache_' . $cachename . '.php';
    } elseif($extra == 'js') {
        $filename = $cachename . '.js';
    }
    if(!is_dir($cachedir)) {
        @mkdir($cachedir, 0777);
    }
    $cachefile = $cachedir . $filename;
    if($fp = @fopen($cachefile, 'wb')) {
        if(!$extra && !$mod) {
            @fwrite($fp, "<?php\r\n//Mudder cache file\r\n//Created on ".date('Y-m-d H:i:s', _G('timestamp'))."\r\n\r\n!defined('IN_MUDDER') && exit('Access Denied');\r\n\r\n".$cachedata."\r\n\r\n?>");
        } elseif($extra == 'js') {
            @fwrite($fp, "//Mudder cache file\r\n//Created on ".date('Y-m-d H:i:s', _G('timestamp'))."\r\n\r\n".$cachedata."\r\n");
        } elseif($mod == 'return') {
            @fwrite($fp, "<?php \r\n!defined('IN_MUDDER') && exit('Access Denied');\r\nreturn $cachedata; \r\n?>");
        }
        @fclose($fp);
        @chmod($cachefile, 0777);
    } else {
        echo 'Can not write to '.$cachename.' cache files, please check directory ./data/cachefiles/ .';
        exit;
    }
}
?>