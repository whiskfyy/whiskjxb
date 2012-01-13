<?php
/**
* 需要检测的文件数组
* @author moufer<moufer@163.com>
* @package 
* @copyright wa666.cn
* @version $Id$
*/
!defined('IN_MUDDER') && exit('Access Denied');

$accessfiles = array(
    array(
        'name' => 'data',
        'access' => '可写',
    ),
    array(
        'name' => 'data/config.php',
        'access' => '可写',
    ),
    array(
        'name' => 'data/config_uc.php',
        'access' => '可写',
    ),
    array(
        'name' => 'data/backupdata',
        'access' => '可写',
    ),
    array(
        'name' => 'data/block',
        'access' => '可写',
    ),
    array(
        'name' => 'data/cachefiles',
        'access' => '可写',
    ),
    array(
        'name' => 'data/datacall',
        'access' => '可写',
    ),
    array(
        'name' => 'data/logs',
        'access' => '可写',
    ),
    array(
        'name' => 'data/templates',
        'access' => '可写',
    ),
    array(
        'name' => 'uploads',
        'access' => '可写',
    ),
    array(
        'name' => 'uploads/adv',
        'access' => '可写',
    ),
    array(
        'name' => 'uploads/bcastr',
        'access' => '可写',
    ),
    array(
        'name' => 'uploads/itemapply',
        'access' => '可写',
    ),
);

?>
