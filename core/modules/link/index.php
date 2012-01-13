<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied'); //安全验证
define('SCRIPTNAV', 'link'); //页面标识

$LK =& $_G['loader']->model('link:mylink'); //加载mylink模型
$links = array();

list(,$list_logo) = $LK->find('*',array('ischeck'=>1,'nq_logo'=>1),'displayorder',0,0,0); //获取有logo的数据
if($list_logo) {
    while($val=$list_logo->fetch_array()) { //循环获得数据库
        $links['logo'][] = $val; //写入一个数组
    }
    $list_logo->free_result(); //释放数据库数据集资源句柄
}
list(,$list_char) = $LK->find('*',array('ischeck'=>1,'logo'=>''),'displayorder',0,0,0); //获取文字链接数据
if($list_char) {
    while($val=$list_char->fetch_array()) {
        $links['char'][] = $val;
    }
    $list_char->free_result();
}

$_HEAD['keywords'] = $MOD['meta_keywords']; //设置标题
$_HEAD['description'] = $MOD['meta_description'];// 设置页面描述
include template('link_index');//载入模板
?>