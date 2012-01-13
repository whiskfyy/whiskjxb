<?php
/**
* 数据库操作
* @author moufer<moufer@163.com>
* @copyright (C)2001-2007 Moufersoft
*/
!defined('IN_MUDDER') && exit('Access Denied');

if(defined('IN_UCENTER')) {
    require_once MUDDER_ROOT . './core/lib/mysql_result.php';
} else {
    $_G['loader']->lib('mysql_result', NULL, 0);
}

class ms_mysql {

    var $dns = '';
	var $query_num = 0;
	var $link = '';

    var $sqls = '';

	function ms_mysql(& $dns) {
		$this->__construct($dns);
	}
	
	function __construct(& $dns) {
        $this->dns = $dns;
        if(DEBUG) $this->sqls = array();
    }

	function connect() {
        $func = $this->dns['pconnect'] ? "mysql_pconnect" : "mysql_connect";
        if(!$this->link = @$func($this->dns['dbhost'], $this->dns['dbuser'], $this->dns['dbpw'], 1)) {
        	$this->_halt("Can not connect to MySQL server");
        }
        if($this->version() > '4.1' && $this->dns['dbcharset']) {
			mysql_query("SET NAMES '" . $this->dns['dbcharset'] . "'", $this->link);
		}
        if($this->version() > '5.0.1') {
			mysql_query("SET sql_mode=''", $this->link);
        }
		if($this->dns['dbname']) {
			if (!@mysql_select_db($this->dns['dbname'], $this->link)) $this->_halt('Cannot use database '.$this->dns['dbname']);
        }
	}

	/**
	 * 选择一个数据库
	 *
	 * @param string $dbname 数据库名
	 */
	function select_db($dbname) {
		$this->dns['dbname'] = $dbname;
		if (!@mysql_select_db($dbname, $this->link)) 
			$this->_halt('Cannot use database '.$dbname);
	}

	/**
	 * 查询数据库版本信息
	 *
	 * @return string
	 */
	function version() {
		return mysql_get_server_info();
	}

	/**
	 * Ping数据库，如果无法ping通，就重连数据库
	 *
	 * @return string
	 */
    function ping() {
        if(!mysql_ping($this->link)) {
            $this->close(); //注意：一定要先执行数据库关闭，这是关键
            $this->connect();
        }
    }

	/**
	 * 发送一条 MySQL 查询
	 *
	 * @param string $SQL SQL语法 
     * @param string $method 查询方式 [空=自动获取并缓存结果集] [unbuffer=并不获取和缓存结果的行]
	 * @return ms_mysql_result 资源标识符
	 */
	function & query($SQL, $method = '') {
        if(DEBUG) {
			$mtime = explode(' ', microtime());
			$starttime = $mtime[1] + $mtime[0];
            $this->sqls[] = 'Query' . ($this->query_num+1) . ':' . $SQL;
        }
        if($this->dns['ping']) $this->ping();
        if($method == 'unbuffer' && function_exists('mysql_unbuffered_query')) {
			$query = mysql_unbuffered_query($SQL, $this->link);
		} else {
			$query = mysql_query($SQL, $this->link);
        }
		if (!$query && $method != 'SILENT') {
            $this->_halt('MySQL Query Error: ' . $SQL);
        }
        $this->query_num++;
        if(DEBUG) {
            $mtime = explode(' ', microtime());
            $querytime = number_format(($mtime[1] + $mtime[0] - $starttime), 6) ;
            $this->sqls[] = 'Time:'.$querytime;
			if(strtolower(substr($SQL,0,7)) == 'select ' && $this->link) {
                if($result = mysql_query('EXPLAIN '.$SQL, $this->link)) {
                    $explain = mysql_fetch_assoc($result);
                    $table = '';
                    if($explain) {
                        $table = '<table border="1" cellspacing="1" cellpadding="1"><tr>';
                        foreach(array_keys($explain) as $key) {
                            $table .= "<td>$key</td>";
                        }
                        $table  .= '</tr><tr>';
                        foreach($explain as $key=>$val) {
                            $table .= "<td>$val</td>";
                        }
                        $table  .= '</tr></table>';
                    }
                }
			}
            $this->sqls[] = $table;
        }
        if(!is_resource($query)) return;
        if(mysql_num_rows($query)) {
            $result = new ms_mysql_result($query);
        } else {
            $result = FALSE;
        }
        return $result;
	}

	/**
	 * 执行一条 MySQL 查询
	 *
	 * @param string $SQL SQL语法 
	 * @param string $method 查询方式 [空=自动获取并缓存结果集] [unbuffer=并不获取和缓存结果的行]
	 * @return resource 资源标识符
	 */
    function exec($SQL, $method = '') {
        if(DEBUG) {
            $mtime = explode(' ', microtime());
			$starttime = $mtime[1] + $mtime[0];
            $this->sqls[] = 'Exec ' . ($this->query_num+1) . ':' . $SQL;
        }
        if($this->dns['ping']) $this->ping();
        if($method == 'unbuffer' && function_exists('mysql_unbuffered_query')) {
			$query = mysql_unbuffered_query($SQL, $this->link);
		} else {
			$query = mysql_query($SQL, $this->link);
        }
		if (!$query && $method != 'SILENT') {
            $this->_halt('MySQL Query Error: ' . $SQL);
        }
        $this->query_num++;
        if(DEBUG) {
            $mtime = explode(' ', microtime());
            $querytime = number_format(($mtime[1] + $mtime[0] - $starttime), 6) ;
            $this->sqls[] = 'Time:'.$querytime;
        }
        return $query;
    }

    /**
     * 返回上一次执行SQL后，被影响修改的条(行)数
     *
     * @return int
     */
	function affected_rows() {
		return mysql_affected_rows($this->link);
	}

	/**
	 * 取得上一步 INSERT 操作产生的 ID 
	 *
	 * @return int
	 */
	function insert_id() {
		return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	/**
	 * 关闭 MySQL 连
	 *
	 * @return bool
	 */
	function close() {
		return mysql_close($this->link);
	}

	/**
	 * 返回上一个 MySQL 操作产生的文本错误信息
	 *
	 * @return string
	 */
    function error() {
        return (($this->link) ? mysql_error($this->link) : mysql_error());
    }

    /**
     * 返回上一个 MySQL 操作中的错误信息的数字编码 
     *
     * @return integer
     */
    function errno() {
        return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
    }

    /**
     * 对字符串处理加入引号 
     *
     * @param string $str
     * @return string
     */
	function _escape_str($str) {
		if (function_exists('mysql_escape_string')) {
			return mysql_escape_string($str);
		} else {
			return addslashes($str);
		}
	}

    /**
     * 过滤字段名 
     *
     * @param string $field
     * @return string
     */
    function _ck_field($field) {
        if(preg_match("/[\'\\\"\<\>]+/", $field)) {
            show_error(lang('global_sql_invalid_field', $field));
        }
        return $field;
    }

    /**
     * 显示错误信息 
     *
     * @param string $msg
     */
	function _halt($msg = '') {
        $charset = $this->global['charset'];
		$message = "<html>\n<head>\n";
		$message .= "<meta content=\"text/html; charset=$charset\" http-equiv=\"Content-Type\">\n";
		$message .= "<style type=\"text/css\">\n";
		$message .=  "body,p,pre {\n";
		$message .=  "font:12px Verdana;\n";
		$message .=  "}\n";
		$message .=  "</style>\n";
		$message .= "</head>\n";
		$message .= "<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#006699\" vlink=\"#5493B4\">\n";

		$message .= "<p>MySQL error:</p><pre><b>".htmlspecialchars($msg)."</b></pre>\n";
		$message .= "<b>Mysql error description</b>: ".htmlspecialchars($this->error())."\n<br />";
		$message .= "<b>Mysql error number</b>: ".$this->errno()."\n<br />";
		$message .= "<b>Date</b>: ".date("Y-m-d @ H:i")."\n<br />";
		$message .= "<b>Script</b>: http://".$_SERVER['HTTP_HOST'].getenv("REQUEST_URI")."\n<br />";

		$message .= "</body>\n</html>";
		echo $message;
		exit;
	}

    /**
     * 显示SQL查询记录 
     *
     */
	function debug_print() {
		global $_G;
        if(!$this->sqls) return;
		$style = 'margin:5px auto;width:98%;line-height:18px;font-family:Courier New;text-align:left;background:#eee;border-width:1px; border-style:solid;border-color:#CCC;';
		$content ='<div style="'.$style.'">';
        $content .='<h3 style="font-size:16px;border-bottom:1px solid #FF9900;margin:5px;padding:0 0 5px;"><a href="javascript:;" onclick="$(\'#debug_sql_history\').toggle();">SQL History</a> ('.count($this->sqls).')</h3>';
		$content .= '<ul style="margin:0;padding:0 0 5px;list-style:none;" id="debug_sql_history">';
		foreach($this->sqls as $val) {
			$content .= '<li style="padding:1px 8px;font-size:12px;">' . $val . '</li>';
		}
		$content .= '</ul></div>';

		return $content;
	}
}

