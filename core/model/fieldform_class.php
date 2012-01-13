<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

class msm_fieldform extends ms_model {

    var $field = array();
    var $config = array();
    var $ctrid = '';
    var $ctrname = '';

    var $style = "";
    var $width = "*";
    var $class = "";
    var $align = "right";

    var $note_width = "150";

    var $is_edit = false;

    function __construct() {
        parent::__construct();
    }

    function msm_fieldform() {
        $this->__construct();
    }

    function & form($field, $value=null, $is_edit=false) {
        $content = '';
        $this->is_edit = $is_edit;
        if(empty($field)) return $content;
        $this->_style();
        $this->field = $field;
        $this->config = $field['config'];
        !is_array($this->config) && (array)$this->config;
        $fun = '_'.$field['type'];
        $this->ctrname = "t_item[{$this->field['fieldname']}]";
        $this->ctrid = $this->_get_id("t_item[{$this->field['fieldname']}]");
        $content = $this->$fun($value);
        return $content;
    }

    function _style() {
        $style = '';
        if($this->width) $style .= " width='$this->width'";
        if($this->class) $style .= " class='$this->class'";
        if($this->align) $style .= " align='$this->align'";
        $this->style = $style;
    }

    function _text($val) {
        $val = $this->is_edit ? $val : $this->config['default'];
        if($this->config['html']) {
            $val = _T($val);
        }
        $notnull = $this->field['allownull'] ? '' : '<span class="font_1">*</span>';
        $validator = '';
        if($notnull) {
            $validator =" validator=\"{'empty':'N','errmsg':'".lang('item_fieldvalidator_empty_field', $this->field['title'])."'}\"";
        }
        $content = "<tr>\r\n";
        $content .= "\t<td $this->style>".$notnull.$this->field['title']."：</td>\r\n";
        $content .= "\t<td><input type=\"text\" class=\"t_input\" name=\"$this->ctrname\" id=\"$this->ctrid\" size=\"{$this->config['size']}\" value=\"$val\"$validator /><div>".$this->field['note']."</div></td>\r\n";
        //$content .= "\t<td class=\"note\">".$this->field['note']."</td>";
        $content .= "</tr>\r\n";
        return $content;
    }

    function _textarea($val) {
		$val = $this->is_edit ? $val : $this->config['default'];
        $notnull = $this->field['allownull'] ? '' : '<span class="font_1">*</span>';
        $validator = '';
        if($notnull) {
            $validator =" validator=\"{'empty':'N','errmsg':'".lang('item_fieldvalidator_empty_field', $this->field['title'])."'}\"";
        }
        if($this->config['html'] == 2) {
            $editor =& $this->loader->lib('editor', NULL, TRUE, $this->ctrname);
            //$editor = new ms_editor("t_item[{$this->field['fieldname']}]", $this->ctrid);
            $editor->set_name(array($this->ctrname,$this->ctrid));
            if($this->in_admin) {
                $editor->item = 'admin';
            }
			$editor->upimage = $this->config['upimage'];
            $editor->height = $this->config['height'];
            $editor->width = $this->config['width'];
            $editor->content = $val;
            $textarea = $editor->create_html();
        } else {
            if($this->config['html']=='1') $val = str_replace('<br />', '', $val);
            $textarea = "<textarea name=\"$this->ctrname\" id=\"$this->ctrid\" style=\"width:{$this->config[width]};height:{$this->config[height]}\"$validator>$val</textarea>";
        }
        $content = "<tr>\r\n";
        $content .= "\t<td valign=\"top\" $this->style>".$notnull.$this->field['title']."：</td>\r\n";
        $content .= "\t<td>$textarea<div>".$this->field['note']."</div></td>\r\n";
        //$content .= "\t<td class=\"note\">".$this->field['note']."</td>";
        $content .= "</tr>\r\n";
        return $content;
    }

    function _option($val) {
        $fun = '__' . $this->config['type'];
        $notnull = $this->field['allownull'] ? '' : '<span class="font_1">*</span>';
        $content = "<tr>\r\n";
        $content .= "\t<td $this->style>".$notnull.$this->field['title']."：</td>\r\n";
        $content .= "\t<td width=\"*\">".$this->$fun($val)."&nbsp;{$this->field['unit']}<div>".$this->field['note']."</div></td>\r\n";
		//$content .= "\t<td class=\"note\"><span class=\"font_3\">".$this->field['note']."</span></td>\r\n";
        $content .= "</tr>\r\n";
        return $content;
    }

    function _numeric($val) {
        $val = $this->is_edit ? $val : $this->config['default'];
        $notnull = $this->field['allownull'] ? '' : '<span class="font_1">*</span>';
        $validator = '';
        if($notnull) {
            $validator =" validator=\"{'empty':'N','errmsg':'".lang('item_fieldvalidator_empty_field', $this->field['title'])."'}\"";
        }
        $content = "<tr>\r\n";
        $content .= "\t<td $this->style>".$notnull.$this->field['title']."：</td>\r\n";
        $content .= "\t<td width=\"*\"><input type=\"text\" class=\"t_input\" name=\"$this->ctrname\" id=\"$this->ctrid\" size=\"{$this->config['size']}\" value=\"$val\"$validator />&nbsp;{$this->field['unit']}<div>".$this->field['note']."</div></td>\r\n";
        //$content .= "\t<td class=\"note\"><span class=\"font_3\">".$this->field['note']."</span></td>\r\n";
        $content .= "</tr>\r\n";
        return $content;
    }

    function _date($val) {
        if($this->is_edit) {
            if($val>0) {
                $val = date($this->config['format'], $val);
            }
        } else {
            if($this->config['default'] == 'NOW') {
                $this->config['default'] = date($this->config['format'],$this->global['timestamp']);
            } elseif($this->config['default'] == 'CUSTOM') {
                $this->config['default'] = trim($this->config['time']);
            }
        }
        return $this->_text($val);
    }

    function _area($val) {
		$this->loader->helper('form');
        $notnull = $this->field['allownull'] ? '' : '<span class="font_1">*</span>';
        //js缓存更新标识
        $jscache_flag = $this->global['cfg']['jscache_flag_area'];
        $content = "<script type=\"text/ajavscript\" src=\"".URLROOT."/data/cachefiles/area.js?r=$jscache_flag\"></script>";
        $content .= "<tr>\r\n";
        $content .= "\t<td $this->style>".$notnull.$this->field['title']."：</td>\r\n";
        $content .= "\t<td>\r\n";
        $content .= "\t<select name=\"t_item[{$this->field['fieldname']}]\">\r\n";
        $content .= form_area($val);
        $content .= "\t\t</select>\r\n";
        $content .= "\t<div>".$this->field['note']."</div></td>\r\n";
        //$content .= "\t<td class=\"note\">".$this->field['note']."</td>";
        $content .= "</tr>\r\n";
        return $content;
    }

    function _mappoint($val) {
		$val = $this->is_edit ? $val : $this->config['default'];
		$id = 'mappoint_' . $this->field['fieldname'];
		list($p1, $p2) = explode(',', $val);
        $notnull = $this->field['allownull'] ? '' : '<span class="font_1">*</span>';
        $validator = '';
        if($notnull) {
            $validator =" validator=\"{'empty':'N','errmsg':'".lang('item_fieldvalidator_empty_field', $this->field['title'])."'}\"";
        }
        $content = "<tr>\r\n";
        $content .= "\t<td $this->style>".$notnull.$this->field['title']."：</td>\r\n";
        $content .= "\t<td><input type=\"text\" class=\"t_input\" name=\"$this->ctrname\" id=\"$id\" size=\"30\" value=\"{$val}\"$validator />&nbsp;&nbsp;<a href=\"javascript:map_mark('$id','$p1','$p2');\">".lang('item_post_selectmappoint')."</a><div>".$this->field['note']."</div></td>\r\n";
        //$content .= "\t<td class=\"note\">".$this->field['note']."</td>";
        $content .= "</tr>\r\n";
        return $content;
    }

    function __select($val) {
        $val = $this->is_edit ? $val : $this->config['default'];
        $option = "<select name=\"$this->ctrname\">";
        $list = explode("\r\n", preg_replace("/\s*(\r\n|\n\r|\n|\r)\s*/", "\r\n", $this->config['value']));
        foreach($list as $sval) {
            $v = explode("=",$sval);
            $selected = $v[0] == $val ? " selected=\"selected\"" : "";
            $option .= "<option value=\"$v[0]\"$selected>$v[1]</option>";
        }
        $option .= "</select>";
        return $option;
    }

    function __check($val) {
        $val = $this->is_edit ? $val : $this->config['default'];
        if($val) $val = explode(",", $val);
        $option = "";
        $list = explode("\r\n", preg_replace("/\s*(\r\n|\n\r|\n|\r)\s*/", "\r\n", $this->config['value']));
        foreach($list as $sval) {
            $v = explode("=",$sval);
            if($val) {
                $checked = in_array($v[0], $val) ? " checked=\"checked\"" : "";
            }
            $option .= "<input type=\"checkbox\" name=\"{$this->ctrname}[]\" id=\"{$this->ctrname}_$v[0]\" value=\"$v[0]\"$checked /><label for=\"{$this->field['fieldname']}_$v[0]\">$v[1]</label>&nbsp;&nbsp;";
        }
        return $option;
    }

    function __radio($val) {
        $val = $this->is_edit ? $val : $this->config['default'];
        $option = "";
        $list = explode("\r\n", preg_replace("/\s*(\r\n|\n\r|\n|\r)\s*/", "\r\n", $this->config['value']));
        foreach($list as $sval) {
            $v = explode("=",$sval);
            if($val) {
                $checked = $v[0] == $val ? " checked=\"checked\"" : "";
            }
            $option .= "<input type=\"radio\" name=\"$this->ctrname\" id=\"{$this->ctrname}_$v[0]\" value=\"$v[0]\"$checked /><label for=\"{$this->field['fieldname']}_$v[0]\">$v[1]</label>&nbsp;&nbsp;";
        }
        return $option;
    }

    function _get_id($name) {
        $id = str_replace(array('[',']'),'_',$name);
        if(substr($id,-1) == '_') $id = substr($id,0,-1);
        return $id;
    }

}

?>