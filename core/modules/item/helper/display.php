<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
class display_item {

    //取得分类的名称或其它
    //参数 catid,keyname
    function category($params) {
        extract($params);
        if(!$keyname) $keyname = 'name';
        if(!$catid) return '';
        $loader =& _G('loader');
        if($catid > 0) {
            $C =& $loader->model('item:category');
            $root_id = $C->get_parent_id($catid);
            if(!$category = $loader->variable('category_' . $root_id, 'item')) return '';
            return $category[$catid]['name'];
        } else {
            $category = $loader->variable('category','item');
            return $category[$catid]['name'];
        }
    }

    //取得选项字段具体值
    //参数 catid,modelid,fieldname,value
    function option($params) {
        extract($params);
        if(!$value) return '';
        $loader =& _G('loader');
        if($catid > 0 && !$modelid) {
            $category = $loader->variable('category', 'item');
            if(isset($category[$catid])) {
                if($category[$catid]['pid'] > 0) {
                    $pid = $category[$catid]['pid'];;
                } else {
                    $pid = $catid;
                }
            }
            $modelid = $category[$pid]['modelid'];
        }
        $fields = $loader->variable('field_'.$modelid, 'item');
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

    //取得tag的云图css样式
    //参数 total
    function tagclassname($params) {
        extract($params);
        $s = array(1=>'f1', 3=>'f2', 5=>'f3', 10=>'f4', 20=>'f4', 30=>'f5', 40=>'f6', 50=>'f7');
        foreach($s as $k => $v) {
            if($total <= $k) return $v;
        }
        return 'f0';
    }

    //获取标签的值
    // 参数 catid,modelid,fieldname,value
    function tag($params) {
        extract($params);
        if(!$value) return '';
        $loader =& _G('loader');
        if($catid > 0 && !$modelid) {
            $category = $loader->variable('category', 'item');
            if(isset($category[$catid])) {
                if($category[$catid]['pid'] > 0) {
                    $pid = $category[$catid]['pid'];;
                } else {
                    $pid = $catid;
                }
            }
            $modelid = $category[$pid]['modelid'];
        }
        $fields = $loader->variable('field_'.$modelid, 'item');
        $field = array();
        foreach($fields as $_val) {
            if($_val['fieldname'] == $fieldname && $_val['type'] == 'tag') {
                $field = $_val;
                break;
            }
        }
        if(!$field) return $value;
        $tags = @unserialize($value);
        $content = '';
        !$field['config']['split'] && $field['config']['split'] = ',';
        if($target) $target = ' target="'.$target.'"';
        foreach($tags as $tagname) {
            $content .= $split . '<a href="' . url('item/tag/tagname/' . $tagname, '', 1) . '"'.$target.'>'  . $tagname . '</a>';
            $split = $field['config']['split'];
        }
        return $content;
    }

    //返回一个主题地址
    //参数 sid,domain
    function url($params) {
        extract($params);
        if(!$sid && !$domain) return '';
        if(!$domain) return url('item/detail/id/'.$sid);
        $loader =& _G('loader');
        $modcfg = $loader->variable('config','item');
        if($modcfg['sldomain']==2 || !$modcfg['base_sldomain']) {
            return url('item/detail/name/'.$domain);
        } else {
            return 'http://' . $domain . '.' . $modcfg['base_sldomain'];
        }
    }
}
?>