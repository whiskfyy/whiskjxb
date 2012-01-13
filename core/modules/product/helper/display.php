<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
class display_product {

    //取得选项字段具体值
    //参数 catid,fieldname,value
    function option($params) {
        extract($params);
        if(!$value) return '';
        $loader =& _G('loader');
        $fields = $loader->variable('field_'.$modelid, 'product');
        $field = array();
        foreach($fields as $_val) {
            if($_val['fieldname'] == $fieldname && $_val['type'] == 'option') {
                $field = $_val;
                break;
            }
        }
        if(!$field) return $value;
        $options = $field['config']['value'];
        $result = '';
        $__val = explode(",", $value);
        $list = explode("\r\n", preg_replace("/\s*(\r\n|\n\r|\n|\r)\s*/", "\r\n", $options));
        $split = '';
        foreach($list as $sval) {
            $v = explode("=",$sval);
            if($__val && in_array($v[0], $__val)) {
                $result .= $split . $v[1];
                $split = $field['config']['display_split'];
            }
        }
        if(!$result) $result = "N/A";
        return $result;
    }

}
?>