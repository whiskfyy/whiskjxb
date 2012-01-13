<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$_G['loader']->model('fieldform', FALSE);
class msm_product_fieldform extends msm_fieldform {

    function __construct() {
        parent::__construct();
        $this->model_flag = 'product';
    }

    function msm_product_fieldform() {
        $this->__construct();
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

}

?>