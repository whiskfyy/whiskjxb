<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

define('SCRIPTNAV', 'map');

if(isset($_GET['p']) && preg_match("/^[a-z0-9\-\.]+[,][a-z0-9\-\.]+$/", $_GET['p'])) {
    list($_GET['p1'], $_GET['p2']) = explode(',', $_GET['p']);
}

$areacode = _get('areacode','','_T');//_T($_GET['areacode']);
$p1 = _get('p1','','_T');//_T($_GET['p1']);
$p2 = _get('p2','','_T');//_T($_GET['p2']);
if($p1 == 0) $p1 = '';
if($p2 == 0) $p2 = '';
$mark = _get('mark') ? 1 : 0;
$show = (_get('show') && $p1 && $p2) ? 1 : 0;
$title = _get('title','','_T');//_T($_GET['title']);
$level = is_numeric($_GET['level']) ? $_GET['level'] : (is_numeric($_CFG['map_view_level']) ? $_CFG['map_view_level'] : 10);

$width = _get('width','500','intval'); //is_numeric($_GET['width']) ? $_GET['width'] : 500;
$height = _get('height','500','intval'); //is_numeric($_GET['height']) ? $_GET['height'] : 500;

if(!$p1 || !$p2) {
	if($area = $_G['loader']->variable('area')) {
        $key = 1;
        if($area[$key]['mappoint']) {
            list($p1,$p2) = explode(',', $area[$key]['mappoint']);
        }
        /*
		foreach(array_keys($area) as $key) {
			if(strlen($key)==2 && $area[$key]['default_mappoint']) {
				list($p1,$p2) = explode(',', $area[$key]['default_mappoint']);
			}
		}
        */
	}
}

$mapflag = $_G['cfg']['mapflag'] ? $_G['cfg']['mapflag'] : '51ditu';
$version = 1.1;
include template('modoer_map');
?>