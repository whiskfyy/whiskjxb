<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$_G['loader']->model('fielddetail', FALSE);
class msm_item_fielddetail extends msm_fielddetail {

    function __construct() {
        parent::__construct();
        $this->model_flag = 'item';
    }

    function msm_item_fielddetail() {
        $this->__construct();
    }

    function _category($val) {
        $C =& $this->loader->model('item:category');
        if(!$pid = $C->get_parent_id($val)) return '';
        $category = $this->loader->variable('category_' . $pid, 'item', false);
        $urlpath = array();
        $urlpath[] = url_path($category[$pid]['name'], url("item/list/catid/$pid"));
        $value = $category[$val];
        if($value['level']>2) {
            $urlpath[] = url_path($category[$value['pid']]['name'], url("item/list/catid/{$value['pid']}"));
        }
        $urlpath[] = url_path($value['name'], url("item/list/catid/{$value['catid']}"));
        return sprintf($this->format, $this->field['title'], implode('&nbsp;&gt;&nbsp;', $urlpath));
    }

    function _member($val) {
        return '';
    }

    function _tag($val) {
        if($val) {
            $concent = '';
            if($tags = unserialize($val)) {
                !$this->config['split'] && $this->config['split'] = ',';
                foreach($tags as $id => $val) {
                    $content .= $split . "<a href=\"".url("item/tag/tagname/$val",'',1)."\">$val</a>";
                    $split = $this->config['split'];
                }
            }
            return sprintf($this->format, $this->field['title'], $content);
        }
        return '';
    }

    function _att($val) {
        if(!$catid = $this->config['catid']) return '';
        $atts = $this->loader->variable('att_list_'.$catid, 'item');
        $content = '';
        if($val) $val = explode(',', $val);
        !$this->config['split'] && $this->config['split'] = ',';
        if($val) foreach($val as $attid) {
            if(!isset($atts[$attid])) continue;
            $name = $atts[$attid];
            if(defined('SUBJECT_CATID') && SUBJECT_CATID > 0) {
                $categorys = $this->loader->variable('category','item');
                if($categorys[SUBJECT_CATID]['level']==1) {
                    if(!empty($categorys[SUBJECT_CATID]['config']['attcat']) && in_array($catid, $categorys[SUBJECT_CATID]['config']['attcat'])) {
                        $name = sprintf("<a href=\"%s\">%s</a>", url("item/list/catid/".SUBJECT_CATID."/att/$catid.$attid"), $name);
                    }
                }
            }
            $content .= $split . $name;
            $split = $this->config['split'];
        }
        return sprintf($this->format, $this->field['title'], $content);
    }

    function _status($val) {
        return sprintf($this->format, $this->field['title'], $val.'&nbsp;'.$this->field['unit']);
    }

    function _template($val) {
        return '';
    }

    function _level($val) {
        return sprintf($this->format, $this->field['title'], $val.'&nbsp;'.$this->field['unit']);
    }

    function _video($val) {
        $url = '<a href="javascript:;" onclick="open_video(\''.$val.'\');">'.lang('item_play_video').'</a>';
        return sprintf($this->format, $this->field['title'], $url.'&nbsp;'.$this->field['unit']);
    }
}

?>