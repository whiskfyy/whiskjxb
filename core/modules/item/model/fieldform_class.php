<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$_G['loader']->model('fieldform', FALSE);
class msm_item_fieldform extends msm_fieldform {

    function __construct() {
        parent::__construct();
    }

    function msm_item_fieldform() {
        $this->__construct();
    }

    function _category($val) {
        $this->loader->helper('form', 'item');
        $notnull = $this->field['allownull'] ? '' : '<span class="font_1">*</span>';
        $validator =" validator=\"{'empty':'N','errmsg':'".lang('item_fieldvalidator_empty_field', $this->field['title'])."'}\"";
        //js缓存更新标识
        $modcfg = $this->loader->variable('config', 'item');
        $jscache_flag = $modcfg['jscache_flag'];
        $content = "<script type=\"text/javascript\" src=\"".URLROOT."/data/cachefiles/item_category.js?r=$jscache_flag\"></script>";
        $content .= "<tr>\r\n";
        $content .= "\t<td $this->style>".$notnull.$this->field['title']."：</td>\r\n";
        $content .= "\t<td>\r\n";
        $content .= "\t<select id=\"category_2\" onchange=\"item_category_select_link(2);\">".form_item_category_sub(ITEM_PID, $val)."</select>\r\n";
        $content .= "\t<select id=\"category_3\" name=\"$this->ctrname\" $validator></select>\r\n";
		$content .= "<script type=\"text/javascript\">\r\n";
        if($val) {
            $content .= "item_category_auto_select('$val');";
        } else {
            $content .= "\$('#category_2').change();";
        }
        $content .= "\r\n</script>\r\n";
        $content .= "\t<div>".$this->field['note']."</div></td>\r\n";
        //$content .= "\t<td class=\"note\">".$this->field['note']."</td>";
        $content .= "</tr>\r\n";
        return $content;
    }

    function _status($val) {
        $notnull = $this->field['allownull'] ? '' : '<span class="font_1">*</span>';
        $content = "<tr>\r\n";
        $content .= "\t<td valign=\"top\" $this->style>".$notnull.$this->field['title']."：</td>\r\n";
        $content .= "\t<td><select name=\"$this->ctrname\" id=\"$this->ctrid\">\r\n";
        $content .= "\t<option value=\"0\"".(!$val?' selected':'').">未审核</option>\r\n";
        $content .= "\t<option value=\"1\"".($val=='1'?' selected':'').">已审核</option>\r\n";
        $content .= "\t</select><div>".$this->field['note']."</div></td>\r\n";
        //$content .= "\t<td class=\"note\">".$this->field['note']."</td>";
        $content .= "</tr>\r\n";
        return $content;
    }

    function _level($val) {
        $notnull = $this->field['allownull'] ? '' : '<span class="font_1">*</span>';
        $content = "<tr>\r\n";
        $content .= "\t<td valign=\"top\" $this->style>".$notnull.$this->field['title']."：</td>\r\n";
        $content .= "\t<td><select name=\"$this->ctrname\" id=\"$this->ctrid\">\r\n";
        for($i=0;$i<=5;$i++) {
			$selected = $i==$val?' selected':'';
            $content .= "\t<option value=\"$i\"".$selected.">$i</option>\r\n";
        }
        $content .= "\t</select><div>".$this->field['note']."</div></td>\r\n";
        //$content .= "\t<td class=\"note\">".$this->field['note']."</td>";
        $content .= "</tr>\r\n";
        return $content;
    }

    function _template($val) {
        $tpllist = $this->loader->variable('templates');
        $notnull = $this->field['allownull'] ? '' : '<span class="font_1">*</span>';
        $content = "<tr>\r\n";
        $content .= "\t<td valign=\"top\" $this->style>".$notnull.$this->field['title']."：</td>\r\n";
        $content .= "\t<td><select name=\"$this->ctrname\" id=\"$this->ctrid\">\r\n";
        $content .= "\t<option value=\"0\">不使用风格</option>\r\n";
        if($tpllist['item']) foreach($tpllist['item'] as $_val) {
			$selected = $_val['templateid']==$val?' selected':'';
            $content .= "\t<option value=\"$_val[templateid]\"".$selected.">$_val[name]</option>\r\n";
        }
        $content .= "\t</select><div>".$this->field['note']."</div></td>\r\n";
        //$content .= "\t<td class=\"note\">".$this->field['note']."</td>";
        $content .= "</tr>\r\n";
        return $content;
    }

    function _tag($val) {
        $val = $val ? unserialize($val) : '';
        $groupid = $this->config['groupid'];
		$val = $val ? $val : $this->config['default'];
        $value = is_string($val) ? implode(',', $val) : (is_array($val) ? $val : '');
        $taggroups = $this->loader->variable('taggroup','item');
        $notnull = $this->field['allownull'] ? '' : '<span class="font_1">*</span>';
        $content = "<tr>\r\n";
        $content .= "\t<td $this->style>".$notnull.$this->field['title']."：</td>\r\n";
        if($taggroups[$groupid]['sort'] == 1) {
            $value = is_array($value) ? implode(',', $value) : $value;
            $content .= "\t<td><input type=\"text\" class=\"t_input\" name=\"$this->ctrname\" id=\"$this->ctrid\" value=\"$value\" size=\"50\" />"."<div class=\"usagetags\" id=\"t_item_taggroup_usetag_{$groupid}\"></div>"."<div>".$this->field['note']."</div></td>\r\n";
        } elseif($taggroups[$groupid]['sort']==2) {
            $tagconfig = explode(',', $taggroups[$groupid]['options']);
            $checkboxs = '';
            foreach($tagconfig as $ky => $tgval) {
                $checked = $value && in_array($tgval, $value) ? ' checked="checked"' : '';
                $checkboxs .= "<input type=\"checkbox\" name=\"{$this->ctrname}[]\"id=\"{$this->ctrid}_{$ky}\" value=\"$tgval\" $checked /><label for=\"{$this->ctrid}_{$ky}\">$tgval</label>&nbsp;";
            }
            $content .= "\t<td>$checkboxs"."<div>".$this->field['note']."</div></td>\r\n";
        }
        //$content .= "\t<td class=\"note\">".$this->field['note']."<span class='font_3'>多个标签，请用\",\"逗号分割</span></td>";
        $content .= "</tr>\r\n";
        return $content;
    }

    function _att($val) {
        $val = $val ? explode(',', $val) : '';
        $catid = $this->config['catid'];
        $len = $this->config['len'] > 0 ? $this->config['len'] : 1;
        $att_list = $this->loader->variable('att_list_'.$catid,'item');
        $notnull = $this->field['allownull'] ? '' : '<span class="font_1">*</span>';
        $content = "<tr>\r\n";
        $content .= "\t<td $this->style>".$notnull.$this->field['title']."：</td>\r\n\t<td>";
        $option = $len == 1 ? ("<select name=\"$this->ctrname\" id=\"$this->ctrid\">") : '';
        if($att_list) foreach($att_list as $attid => $sv) {
            if($len > 1) {
                $checked = is_array($val) && in_array($attid, $val) ? " checked=\"checked\"" : "";
                $option .= "<input type=\"checkbox\" name=\"{$this->ctrname}[]\" value=\"$attid\" id=\"{$this->ctrid}_$attid\"$checked /><label for=\"{$this->ctrid}_$attid\">$sv</label>&nbsp;&nbsp;";
            } else {
                $selected = is_array($val) && in_array($attid, $val) ? " selected=\"selected\"" : "";
                $option .= "\r\n\t<option value=\"$attid\" id=\"{$this->field['fieldname']}_$attid\"$selected />$sv</option>";
            }
        }
        $len == 1 && $option .= "\r\n\t</select>";
        //$content .= "\t<td class=\"note\">".$this->field['note']."<span class='font_3'>多个标签，请用\",\"逗号分割</span></td>";
        $content .= $option . "</td>\r\n\t</tr>\r\n";
        return $content;
    }

    function _member($val) {
        return $this->_text($val);
    }

    function _video($val) {
        $this->config['size'] = '60';
        return $this->_text($val);
    }

}

?>