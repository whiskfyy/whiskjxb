<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
(!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied');

$H =& $_G['loader']->model('hook');
$op = $_GET['op'] ? $_GET['op'] : $_POST['op'];
?>