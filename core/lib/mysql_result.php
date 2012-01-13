<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

class ms_mysql_result {

    var $res = null;

	function __construct($res) {
        $this->res = $res;
    }

	function ms_mysql_result($res) {
		$this->__construct($res);
	}

    function data_seek($row_number) {
        return mysql_data_seek($this->res, row_number);
    }

    function mysql_db_name($row) {
        return mysql_db_name($this->res, $row);
    }

    function result($row) {
        return mysql_result($this->res, $row);
    }

    function fetch_array($result_type = MYSQL_ASSOC) {
        //MYSQL_ASSOC，MYSQL_NUM 和 MYSQL_BOTH
        return mysql_fetch_array($this->res, $result_type);
    }

    //获取数据，简化函数
    function fetch($result_type = MYSQL_ASSOC) {
        //MYSQL_ASSOC，MYSQL_NUM 和 MYSQL_BOTH
        return mysql_fetch_array($this->res, $result_type);
    }

    function fetch_row() {
        return mysql_fetch_row($this->res);
    }

    function fetch_assoc() {
        return mysql_fetch_assoc($this->res);
    }

    function fetch_field($field_offset=null) {
        if(!$field_offset) {
            return mysql_fetch_field($this->res);
        } else {
            return mysql_fetch_field($this->res, field_offset);
        }
    }

    function fetch_lengths() {
        return mysql_fetch_lengths($this->res);
    }

    function fetch_object() {
        return mysql_fetch_object($this->res);
    }

    function field_len($field_offset) {
        return mysql_field_len($this->res, $field_offset);
    }

    function field_name($field_offset) {
        return mysql_field_name($this->res, $field_offset);
    }

    function field_seek($field_offset) {
        return mysql_field_seek($this->res, $field_offset);
    }

    function field_table($field_offset) {
        return mysql_field_table($this->res, $field_offset);
    }

    function field_type($field_offset) {
        return mysql_field_type($this->res, $field_offset);
    }

    function num_fields() {
        return mysql_num_fields($this->res);
    }

    function num_rows() {
        return mysql_num_rows($this->res);
    }

    function free_result() {
        return mysql_free_result($this->res);
    }

    //释放SQL资源，简化函数
    function free() {
        return mysql_free_result($this->res);
    }

}

