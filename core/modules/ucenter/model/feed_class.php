<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

class msm_ucenter_feed extends ms_model {

    var $table = 'dbpre_memberfeed';
    var $key = 'id';

    var $cfg = array();

    function __construct() {
        parent::__construct();
        $this->model_flag = 'ucenter';
        $this->cfg = $this->variable('config');
    }
    
    function msm_ucenter_feed() {
        $this->__construct();
    }

    //检测本功能是否开启
    function enabled() {
        return $this->cfg['uc_feed'];
    }

    function read($feedids) {
    }

    function find() {
    }

    function all($limit = 100) {
        return uc_feed_get($limit);
    }

    function add($icon, $uid, $username, $title_template, $title_data, $body_template, $body_data, $body_general, $images = null) {
        if(!$this->cfg['uc_feed']) return;
        return uc_feed_add($icon, $uid, $username, $title_template, $title_data, $body_template, $body_data, $body_general, '', $images);
    }

    function delete($feedids) {
    }
}