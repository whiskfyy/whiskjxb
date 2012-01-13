<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
(!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied');

$C =& $_G['loader']->model('config');
$op = $_GET['op'];

switch($op) {
case 'post':
    if(!is_array($_POST['point'])) redirect('global_op_unselect');
    $post = array();
    $post['point'] = serialize(new_intval($_POST['point']));
    $C->save($post, MOD_FLAG);
    redirect('global_op_succeed', cpurl($module,$act,'setting'));
    break;
default:
    $op = 'setting';
    $point = $C->read('point', MOD_FLAG);
    $point = unserialize($point['value']);
    $list = read_point_rule();
    $admin->tplname = cptpl('point', MOD_FLAG);
}

function & read_point_rule() {
    global $_G;
    $result = array();
    $modules =& $_G['modules'];
    foreach($modules as $key => $val) {
        $file = MUDDER_MODULE . $val['flag'] . DS .'inc' . DS . 'point_rule.php';
        if(!$rule = read_cache($file)) continue;
        if(!is_array($rule)) continue;
        $result = array_merge($result, $rule);
    }
    return $result;
}
?>