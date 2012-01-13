<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

class msm_fielddetail extends ms_model {

    // 字段属性
    var $field = array();
    var $config = array();

    var $pagemod = 'detail';
    var $format = "";
    var $style = "";
    var $width = "*";
    var $class = "";
    var $align = "right";
    var $td_num = 2;

    var $note_width = "150";

    function __construct() {
        parent::__construct();
    }

    function msm_fielddetail() {
        $this->__construct();
    }

    function style() {
        $style = '';
        if($this->width) $style .= " width='$this->width'";
        if($this->class) $style .= " class='$this->class'";
        if($this->align) $style .= " align='$this->align'";
        $this->style = $style;

        $this->format = "<tr>\r\n";
        $this->format .= "\t<td $this->style>%s：" . ($this->td_num > 1 ? "</td>\r\n" : "");
        $this->format .= ($this->td_num > 1 ? "\t<td width=\"*\">" : "")."%s</td>\r\n";
        $this->format .= "</tr>\r\n";
    }

    function detail($field, $val=null) {
        $content = '';
        if(empty($field)) return $content;
        $this->style();
        $this->field = $field;
        $this->config = $field['config'];
        !is_array($this->config) && (array)$this->config;
        $fun = '_'.$field['type'];
        if(empty($val)) {
            return;
        }
        return $this->$fun($val);
    }

    function __template_parse(& $val) {
        $re = array('{value}','{urlcode:value}');
        $va = array($val, rawurlencode($val));
        return str_replace($re,$va,$this->field['template']);
    }

    function _text($val) {
        $text = $val . ($this->field['unit'] ? "&nbsp;{$this->field['unit']}" : '');
        if($this->field['template']) {
            $text = $this->__template_parse($text);
        }
        return sprintf($this->format, $this->field['title'], $text);
    }

    function _numeric($val) {
        return $this->_text($val);
    }

    function _textarea($val) {
        return $this->_text($val);
    }

    function _option($val) {
        $fun = '__' . $this->config['type'];
        $val = $this->$fun($val);
        return $this->_text($val);
    }

    function _date($val) {
        return $this->_text(newdate($val,$this->config['format']));
    }

    function _area($val) {
        $area = $this->loader->variable('area_1');
        // 载入地区
        if($area[$val]['level'] == 2) {
            $paid = 0;
        } else {
            $paid = $area[$val]['pid'];
        }
        $urlpath = array();
        if($paid) {
            $urlpath[] = url_path($area[$paid]['name'], url('item/list/catid/' . SUBJECT_CATID . '/aid/'.$paid));
        }
        if($paid != $val) {
            $urlpath[] = url_path($area[$val]['name'], url('item/list/catid/' . SUBJECT_CATID . '/aid/'.$val));
        }
        return sprintf($this->format, $this->field['title'], implode('&nbsp;&gt;&nbsp;', $urlpath));
    }

    function __select($val) {
        $option = "";
        $list = explode("\r\n", preg_replace("/\s*(\r\n|\n\r|\n|\r)\s*/", "\r\n", $this->config['value']));
        foreach($list as $sval) {
            $v = explode("=",$sval);
            if($v[0] == $val) {
                $option = $v[1];
            }
        }
        if(!$option) $option = "N/A";
        return $option;
    }

    function __check($val) {
        if($val) $val = explode(",", $val);
        $option = "";
        $list = explode("\r\n", preg_replace("/\s*(\r\n|\n\r|\n|\r)\s*/", "\r\n", $this->config['value']));
        !$this->config['display_split'] && $this->config['display_split'] = '&nbsp;';
        if($this->config['display_split'])
        foreach($list as $sval) {
            $v = explode("=",$sval);
            if($val && in_array($v[0], $val)) {
                $option .= $split . $v[1];
                $split = $this->config['display_split'];
            }
        }
        if(!$option) $option = "N/A";
        return $option;
    }

    function __radio($val) {
        return $this->__select($val);
    }
}

?>