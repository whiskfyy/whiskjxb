<?php
(!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied');
$modmenus = array(
    array(
        'title' => '会员功能设置',
        'member|模块设置|config',
        'member|积分设置|point',
    ),
    array(
        'title' => '会员管理',
        'member|用户组|usergroup',
        'member|用户列表|members',
    ),
    array(
        'title' => '其他功能',
        'member|通行证反向整合|passport',
        'member|短信息通知|batchpm',
    ),
);
?>