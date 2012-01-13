<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
class ms_editor {

    var $id = '';
    var $name = '';
    var $width = '100%';
    var $height = '300px';
    var $css = '';
    var $content = '';
    var $item = 'basic';
    var $upimage = false;

    var $isload = false;
    var $items = array();

    function __construct($name) {
        $this->set_name($name);
    }

    function ms_editor($name) {
        $this->__construct($name);
    }

    function set_name($name) {
        if(is_array($name)) {
            $this->name = $name[0];
            $this->id = $name[1];
        } else {
            $this->name = $name;
            $this->id = $name;
        }
    }

    function create_html() {
        $this->_init_item();
        $content = '';
        if(!$this->isload) {
            $content .= '<script type="text/javascript" charset="utf-8" src="'.url('modoer',0,1).'static/editor/kindeditor.js"></script>'."\r\n";
        }
        $content .= "<script type=\"text/javascript\">\r\n";
        $content .= "\tKE.show({ \r\n";
        $content .= "\t\tid : '$this->id', \r\n";
        $this->css && $content .= "\t\cssPath : '$this->css', \r\n";
        $this->item != 'default' && $content .= "\t\titems : {$this->items[$this->item]} \r\n";
        $content .= "\t});\r\n";
        $content .= "</script>\r\n";
        $content .= "<style type=\"text/css\">.ke-toolbar-table td{padding:0;}</style>";
        $content .= "<textarea id=\"$this->id\" name=\"$this->name\" style=\"width:$this->width;height:$this->height;visibility:hidden;\">$this->content</textarea>";

        $this->isload = true;

        return $content;
    }

    function create_html2($id, $name, $content='', $item='default', $width='100%', $height='300px',$loadjs = false) {
        $this->_init_item();
        $content = '';
        $loadjs && $content .= '<script type="text/javascript" charset="utf-8" src="'.URLROOT.'/static/editor/kindeditor.js"></script>'."\r\n";
        $content .= "<script type=\"text/javascript\">\r\n";
        $content .= "\tKE.show({ \r\n";
        $content .= "\t\tid : '$id', \r\n";
        $item != 'default' && $content .= "\t\titems : {$this->items[$item]} \r\n";
        $content .= "\t});\r\n";
        $content .= "</script>\r\n";
        $content .= "<style type=\"text/css\">.ke-toolbar-table td{padding:0;}</style>";
        $content .= "<textarea id=\"$id\" name=\"$name\" style=\"width:$width;height:$height;visibility:hidden;\">$content</textarea>";

        if($loadjs) $this->isload = true;

        return $content;
    }

    function _init_item() {
        $image = $this->upimage ? "'image','flash'" : '';

        $this->items['default'] = "
            ['source', 'preview', 'fullscreen', 'undo', 'redo', 'print', 'cut', 'copy', 'paste', 
            'plainpaste', 'wordpaste', 'justifyleft', 'justifycenter', 'justifyright', 
            'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript', 
            'superscript', 'date', 'time', '-', 
            'title', 'fontname', 'fontsize', 'textcolor', 'bgcolor', 'bold', 
            'italic', 'underline', 'strikethrough', 'removeformat', 'selectall', 'image', 
            'flash', 'media', 'layer', 'table', 'specialchar', 'hr', 
            'emoticons', 'link', 'unlink', 'about']
        ";
        $this->items['admin'] = $this->items['default'];
        $this->items['basic'] = "
            ['fullscreen', 'undo', 'redo', 'fontname', 'fontsize', 'textcolor', 'bgcolor', 'bold', 'italic', 'underline',
            'removeformat', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', 
            'hr', 'link', 'unlink', 'table', 'emoticons', 'removeformat', 'wordpaste',
            $image]
        ";
    }

}
?>