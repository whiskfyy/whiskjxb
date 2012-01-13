<?php
/**
* URL改写类
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
class ms_urlrewriter {
    
    var $list = '';
    var $mod = 'html'; //html and pathinfo
    var $hide_index = false;

    var $config_file = '';

    function __construct() {
    }
    
    function ms_urlrewriter() {
        $this->__construct();
    }

    function set_mod($mod) {
        if(!$mod) $mod = 'html';
        if($mod == $this->mod && $this->config_file) return;
        $this->mod = $mod;
        $this->config_file = MUDDER_DATA . 'rewrite_'.$this->mod.'.inc';
        $this->_load_config();
    }

    function _load_config($reload = FALSE) {
        if($this->list && !$reload) return;
        if(!is_file($this->config_file)) return;
        $content = @file_get_contents($this->config_file);
        $content = explode("\n", preg_replace("/\s*(\r\n|\n\r|\n|\r)\s*/", "\n", $content));
        foreach($content as $k => $v) {
            if(trim($v)=='') continue;
            $tmp = explode(" ",$v);
            $tmp2 = array();
            foreach($tmp as $kk => $vv) {
                if($vv) $tmp2[] = $vv;
            }
            if(!$tmp2) continue;
            $this->list[] = $tmp2;
        }
    }

    //解析伪静态URL
    function html_recover($rewrite) {
        if(!$rewrite) return;
        if(!$url = $this->preg_recover($rewrite)) {
            $url = $this->_html_recover($rewrite);
        } else {
            $output = parse_url($url);
            $_GET['m'] = basename($output['path'], '.php');
            $url = $output['query'];
        }
        parse_str($url, $output);
        foreach($output as $k => $v) {
            $_GET[$k] = $v;
        }
    }

    //解析目录形式URL
    function pathinfo_recover($pathinfo) {
        if(!$pathinfo) return;
        if(!$url = $this->preg_recover($pathinfo)) {
            $url = $this->_pathinfo_recover($pathinfo);
        } else {
            $output = parse_url($url);
            $_GET['m'] = basename($output['path'], '.php');
            $url = $output['query'];
        }
        parse_str($url, $output);
        foreach($output as $k => $v) {
            $_GET[$k] = $v;
        }
    }

    //解析index.php模拟
    function index_recover() {
        // 获取请求的URI
        foreach (array('REQUEST_URI', 'HTTP_X_REWRITE_URL', 'argv') as $var) {
            if ($uri = $_SERVER[$var]) {
                if ($var == 'argv') {
                    $uri = $uri[0];
                }
                break;
            }
        }
        // 去除//情况
        $uri = str_replace('//', '/', $uri);
        if (strpos($uri, 'index.php') !== false) {
            $uri = explode('index.php', $uri, 2);
        }
        // 如果没有请求的字符串返回
        if (!isset($uri[1])) {
            $_GET['m'] = '';
            $_GET['act'] = '';
            return;
        }
        // 解析
        if($uri[1]{0} == '/') $uri[1] = substr($uri[1], 1);
        if(strposex($uri[1], '.html')) {
            $this->html_recover($uri[1]);
        } else {
            $this->pathinfo_recover($uri[1]);
        }
    }

    function preg_parse($paramstr) {
        $urlpre = $this->hide_index ? '' : 'index.php/';
        if($this->list) foreach($this->list as $k => $val) {
            if(preg_match("/^$val[0]$/", $paramstr)) {
            	if(!isset($val[1])) $val[1] = '';
                return $urlpre . preg_replace("/^$val[0]$/", "$val[1]", $paramstr);
            }
        }
        $fun = $this->mod == 'pathinfo' ? '_pathinfo_parse' : '_html_parse';
        return $this->$fun($paramstr);
    }

    function preg_recover($paramstr) {
        if(!$this->list) return FALSE;
        foreach($this->list as $k => $val) {
            if(preg_match("/^$val[0]$/", $paramstr)) {
                return preg_replace("/^$val[0]$/", "$val[1]", $paramstr);
            }
        }
        return FALSE;
    }

    function _html_recover($rewrite) {
        if(!$rewrite) return;
        $rewrite = basename($rewrite, '.html');
        $arr_param = explode('-', $rewrite);
        $_GET['m'] = $arr_param[0];
        $url = $split = '';
        foreach($arr_param as $k => $v) {
            $v = str_replace('_f_','-',$v);
            if($k > 0) {
                $url .= $split . $v;
            }
            if($k == 0) {
                $split = 'act=';
            } elseif($k%2 == 1) {
                $split = '&';
            } elseif($k%2 == 0) {
                $split = '=';
            }
        }
        return $url;
    }

    function _pathinfo_recover($pathinfo) {
        if(!$pathinfo) return;
        $arr_param = explode('/', $pathinfo);
        $_GET['m'] = $arr_param[0];
        $url = $split = '';
        $params = explode('-', $arr_param[1]);
        if($params) foreach($params as $k => $v) {
            $v = str_replace('_f_','-',$v);
            if($k == 0) {
                $split = 'act=';
            } elseif($k%2 == 1) {
                $split = '&';
            } elseif($k%2 == 0) {
                $split = '=';
            }
            $url .= $split . $v;
        }
        return $url;
    }

    function _html_parse($paramstr) {
        $urlpre = $this->hide_index ? '' : 'index.php/';
        if(preg_match("/^([a-z0-9_]+)\.php$/i", $paramstr, $match)) {
            return $urlpre . $match[1].'.html';
        } else {
            return $urlpre . str_replace(array('.php?act=','&amp;','=', '&'),'-', $paramstr) . '.html';
        }
    }

    function _pathinfo_parse($paramstr) {
        $urlpre = $this->hide_index ? '' : 'index.php/';
        if(preg_match("/^([a-z0-9_]+)\.php$/i", $paramstr, $match)) {
            return $urlpre . $match[1];
        } else {
            return $urlpre . str_replace(array('.php?act=','&amp;','=', '&'),array('/','-','-','-'), $paramstr);
        }
    }

}
?>