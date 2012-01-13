<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
return array(
    'content' => array (
        'fieldname' => 'content',
        'title' => '详细介绍',
        'note' => '',
        'type' => 'textarea',
        'listorder' => '90',
        'allownull' => '0',
        'enablesearch' => '0',
        'iscore' => '0',
        'isadminfield' => '0',
        'show_list' => '0',
        'show_detail' => '1',
        'regular' => '',
        'errmsg' => '',
        'datatype' => 'MEDIUMTEXT',
        'config' => 
        array (
          'width' => '99%',
          'height' => '200px',
          'html' => '2',
          'default' => '',
          'charnum_sup' => 0,
          'charnum_sub' => 1000,
        ),
    ),
    'templateid' => array (
        'fieldname'=>'templateid',
        'title'=>'主题风格',
        'note'=>'',
        'type'=>'template',
        'listorder'=>'98',
        'allownull'=>'0',
        'enablesearch'=>'0',
        'iscore'=>'1',
        'isadminfield'=>'1',
        'regular'=>'/[0-9]+/',
        'errmsg'=>'无效的主题风格，请返回设置。',
        'datatype' => 'smallint(5) UNSIGNED',
        'config'=> array (
            'default' => 0,
        ),
    ),
);
?>