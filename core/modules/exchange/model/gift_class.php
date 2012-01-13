<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

class msm_exchange_gift extends ms_model {

    var $table = 'dbpre_exchange_gifts';
    var $key = 'giftid';
    var $model_flag = 'exchange';

    function __construct() {
        parent::__construct();
        $this->model_flag = 'exchange';
        $this->modcfg = $this->variable('config');
        $this->init_field();
    }

    function msm_exchange_gift() {
        $this->__construct();
    }

    function init_field() {
        $this->add_field('name,available,displayorder,description,price,num');
        $this->add_field_fun('available,displayorder,price,num', 'intval');
        $this->add_field_fun('name', '_T');
        $this->add_field_fun('description', '_HTML');
    }

    function save($post,$giftid=null) {
        $edit = $giftid != null;
        if($edit) {
            if(!$detail = $this->read($giftid)) redirect('exchange_gift_empty');
        }
        //上传图片部分
        if(!empty($_FILES['picture']['name'])) {
            $this->loader->lib('upload_image', NULL, FALSE);
            $img = new ms_upload_image('picture', $this->global['cfg']['picture_ext']);
            $this->upload_thumb($img);
            $post['picture'] = str_replace(DS, '/', $img->path . '/' . $img->filename);
            $post['thumb'] = str_replace(DS, '/', $img->path . '/' . $img->thumb_filenames['thumb']['filename']);
        }
        $giftid = parent::save($post, $giftid, false, true, true);
        if($edit && $post['picture']) {
            @unlink(MUDDER_ROOT . $detail['picture']);
            @unlink(MUDDER_ROOT . $detail['thumb']);
        }
        return $giftid;
    }

    function delete($giftids) {
        $giftids = parent::get_keyids($giftids);
        parent::delete($giftids);
    }

    function update($post) {
        if(!is_array($post)) redirect('global_op_unselect');
        foreach($post as $id => $val) {
            $val['available'] = (int)$val['available'];
            $this->db->from($this->table);
            $this->db->set($val);
            $this->db->where('giftid', $id);
            $this->db->update();
        }
    }

    //上传图片
    function upload_thumb(& $img) {
        $thumb_w = $this->modcfg['thumb_w'] ? $this->modcfg['thumb_w'] : 160;
        $thumb_h = $this->modcfg['thumb_h'] ? $this->modcfg['thumb_h'] : 100;

        $img->set_max_size($this->global['cfg']['picture_upload_size']);
        $img->userWatermark = $this->global['cfg']['watermark'];
        $img->watermark_postion = $this->global['cfg']['watermark_postion'];
        $img->thumb_mod = $this->global['cfg']['picture_createthumb_mod'];
        //$img->limit_ext = array('jpg','png','gif');
        $img->set_ext($this->global['cfg']['picture_ext']);
        $img->set_thumb_level($this->global['cfg']['picture_createthumb_level']);
        $img->add_thumb('thumb', 'thumb_', $thumb_w, $thumb_h);
        $img->upload('exchange');
    }

    function check_post(& $post, $edit = false) {
        if(!$post['name']) redirect('exchangecp_gift_name_empty');
        if(!$post['price']) redirect('exchangecp_gift_price_empty');
        if(!$post['num']) redirect('exchangecp_gift_num_empty');
        if(!$post['picture'] && !$edit) redirect('exchangecp_gift_picture_empty');
        if(!$post['description']) redirect('exchangecp_gift_description_empty');
    }

    //销售
    function salevolume($giftid,$num=1) {
        if(!$giftid || !$num) return;
        $this->db->from($this->table);
        if($num > 0) {
            $this->db->set_add('salevolume',$num);
            $this->db->set_dec('num',$num);
        } else {
            $this->db->set_dec('salevolume',abs($num));
            $this->db->set_add('num',abs($num));
        }
        $this->db->where('giftid',$giftid);
        $this->db->update();
    }

    //人气
    function pageview($giftid,$num=1) {
        $this->db->from($this->table);
        $this->db->set_add('pageview',$num);
        $this->db->where('giftid',$giftid);
        $this->db->update();
    }

}
?>