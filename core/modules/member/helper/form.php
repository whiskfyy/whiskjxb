<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
function form_member_usergroup($select = '', $type = null) {
	$loader =& _G('loader');
	$usergroups = $loader->variable('usergroup', 'member');
    if($type && is_string($type)) $type = array($type);
	foreach($usergroups as $key => $val) {
        if($type && !in_array($val['grouptype'],$type)) continue;
		$selected = $key == $select ? ' selected' : '';
		$content .= "\t<option value=\"$key\"$selected>$val[groupname]</option>\r\n";
	}
	return $content;
}
?>