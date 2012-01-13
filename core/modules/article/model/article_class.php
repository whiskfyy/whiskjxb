<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

class msm_article extends ms_model {

    var $table = 'dbpre_articles';
    var $field_table = 'dbpre_article_data';
    var $key = 'articleid';
    var $model_flag = 'article';

    function __construct() {
        parent::__construct();
        $this->model_flag = 'article';
        $this->modcfg = $this->variable('config');
        $this->init_field();
    }

    function msm_article() {
        $this->__construct();
    }

    function init_field() {
        $this->add_field('catid,sid,subject,att,uid,author,copyfrom,keywords,introduce,status,closed_comment,content');
        $this->add_field_fun('catid,sid,att,uid', 'intval');
        $this->add_field_fun('subject,author,copyfrom,introduce', '_T');
        $this->add_field_fun('content', '_HTML');
    }

    function read($articleid,$select='*') {
        $join_field = $select=='*' || strposex($select,'content');
        if($join_field)  {
            $this->db->join($this->table,'a.articleid',$this->field_table,'ad.articleid');
        } else  {
            $this->db->from($this->table, 'a');
        }
        $this->db->select($select);
        $this->db->where('a.articleid',$articleid);
        $result = $this->db->get_one();
        return $result;
    }

    function search($select,$orderby,$start,$offset) {
        $this->db->from($this->table);
        if(is_numeric($_GET['catid']) && $_GET['catid'] > 0) {
            $this->loader->helper('misc','article');
            $this->db->where_in('catid',misc_article::category_catids($_GET['catid']));
        }
        if(is_numeric( $_GET['sid'] ) && $_GET['sid'] > 0) $this->db->where('sid', (int)$_GET['sid']);
        isset($_GET['status']) && $this->db->where('status', (int)$_GET['status']);
        if($_GET['keyword']) {
            $this->db->where_like('subject', '%'.$_GET['keyword'].'%');
        }
        $result = array(0,'');
        if(!$result[0] = $this->db->count()) {
            return $result;
        }
        $this->db->sql_roll_back('from,where');
		$this->db->select($select?$select:'*');
        if($orderby) $this->db->order_by($orderby);
        if($start < 0) {
            if(!$result[0]) {
                $start = 0;
            } else {
                $start = (ceil($result[0]/$offset) - abs($start)) * $offset; //按 负数页数 换算读取位置
            }
        }
        $this->db->limit($start, $offset);
        $result[1] = $this->db->get();

        return $result;
    }

    function save($post,$articleid=null,$role='member') {
        $edit = $articleid != null;
        if($this->modcfg['post_filter']) {
            $W =& $this->loader->model('word');
        }
        if($edit) {
            if(!$detail = $this->read($articleid)) redirect('article_empty');
            if(!$this->in_admin) {
                unset($post['status']);
            }
            //判断权限
            $access = $this->check_post_access('edit',$role,$detail['sid'],$this->global['user']->uid);
        } else {
            $post['dateline'] = $this->global['timestamp'];
            if(!$this->in_admin) {
                $post['uid'] = $this->global['user']->uid;
                $post['author'] = $this->global['user']->username;
                $post['status'] = $this->modcfg['post_check'] ? 0 : 1;
            } else {
                $post['uid'] = 0;
            }
            if($this->modcfg['post_filter']) {
                $W->check($post['content']) && $post['status'] = 0;
            }
            //判断权限
            $access = $this->check_post_access('add',$role,$post['sid'],$post['uid']);
        }
        if(!$access) redirect('global_op_access');
        if($this->modcfg['post_filter']) {
            $post['content'] = $W->filter($post['content']);
        }
        //上传图片部分
        if(!empty($_FILES['picture']['name'])) {
            $this->loader->lib('upload_image', NULL, FALSE);
            $img = new ms_upload_image('picture', $this->global['cfg']['picture_ext']);
            $this->upload_thumb($img);
            $post['havepic'] = 1;
            $post['picture'] = str_replace(DS, '/', $img->path . '/' . $img->filename);
            $post['thumb'] = str_replace(DS, '/', $img->path . '/' . $img->thumb_filenames['thumb']['filename']);
        }
        //转换
        $post = $this->convert_post($post);
        //检测
        $detail && $post = array_merge($detail, $post);
        $this->check_post($post,$edit,$role);
        //过滤
        if($detail) {
            foreach($detail as $key => $val) {
                if(isset($post[$key]) && $val == $post[$key]) {
                    unset($post[$key]);
                }
            }
        }
        if($post) {
            if(isset($post['content'])) {
                $content = $post['content'];
                unset($post['content']);
            }
            $post && $articleid = parent::save($post, $articleid, 0, 0, 0);
        } else {
            define('RETURN_EVENT_ID', $detail['status'] ? 'global_op_succeed' : 'global_op_succeed_check');
            return $articleid;
        }
        if($edit && $post['thumb']) {
            @unlink(MUDDER_ROOT . $detail['thumb']);
            @unlink(MUDDER_ROOT . $detail['picture']);
        }
        $content && $this->save_field($content,$articleid,$edit);

        //统计
        //更新分类统计
        $status = FALSE;
        if(!$edit && $post['status'] == 1) {
            $this->category_num_add($post['catid'], 1); //新入不需要审核+1
            $this->user_point_add($post['uid']); //会员积分
            !$this->in_admin && $this->_feed($articleid); //feed
            $status = TRUE;
        } elseif($edit) {
            if(isset($post['catid']) && $detail['catid'] != $post['catid']) { //是否更换了分类
                if($detail['status'] == 1) $this->category_num_dec($detail['catid']); //原分类通过审核的数量-1
                if((isset($post['status']) && $post['status']==1)||(!isset($post['status']) && $detail['status']==1)) {
                    $this->category_num_add($detail['catid']); //新分类数量+1
                }
            } else {
                if($detail['status'] != 1 && $post['status'] == 1) {
                    $this->category_num_add($detail['catid']); //通过审核+1
                    $this->user_point_add($detail['uid']);
                    $detail['uid'] > 0 && $this->_feed($articleid); //feed
                } elseif($detail['status'] == 1 && isset($post['status']) && $post['status'] != 1) {
                    $this->category_num_dec($detail['catid']); //更改审核状态-1
                }
            }
            $status = $detail['status'] == 1; //返回表示旨在前台使用，不必考虑后台
        }
        define('RETURN_EVENT_ID', $status ? 'global_op_succeed' : 'global_op_succeed_check');

        return $articleid;
    }

    function save_field($content, $articleid, $edit) {
        $this->db->from($this->field_table);
        $this->db->set('content',$content);
        if($edit) {
            $this->db->where('articleid',$articleid);
            $this->db->update();
        } else {
            $this->db->set('articleid',$articleid);
            $this->db->insert();
        }
    }

    function check_post(&$post, $edit=false, $role = 'member') {
        if(!$post['subject']) redirect('article_post_subject_empty');
        if(strlen($post['subject'])<2 || strlen($post['subject'])>60) redirect(lang('article_post_subject_len',array(2,60)));
        if(!$post['catid']) redirect('article_post_catid_empty');
        if(!$post['author']) redirect('article_post_author_empty');
        if(!$post['introduce']) redirect('article_post_introduce_empty');
        if(strlen($post['introduce'])<10 || strlen($post['introduce'])>255) redirect(lang('article_post_introduce_len',array(10,255)));
        if(!$post['content']) redirect('article_post_content_empty');
        if(strlen($post['content'])<10 || strlen($post['content'])>20000) redirect(lang('article_post_content_len',array(10,20000)));
        if(!$this->in_admin) {
            if($role=='owner' && !$post['sid']) redirect('article_post_sid_empty');
            if($role!='owner' && $post['sid']>0 && !$this->modcfg['member_bysubject']) redirect('article_post_sid_member_disable');
        }
    }

    function checkup($ids,$uppoint=true) {
        $ids = parent::get_keyids($ids);
        $this->db->from($this->table);
        $this->db->where_in('articleid',$ids);
        $this->db->where('status',0);
        $this->db->select('articleid,uid,status,catid,thumb,picture');
        if(!$q=$this->db->get()) return;
        $artids = $catids = $uids = array();
        while($v=$q->fetch_array()) {
            $artids[] = $v['articleid'];
            if(isset($catids[$v['catid']])) {
                $catids[$v['catid']]++;
            } else {
                $catids[$v['catid']] = 1;
            }
            if(!$uppoint||!$v['uid']) continue;
            if(isset($uids[$v['uid']])) {
                $uids[$v['uid']]++;
            } else {
                $uids[$v['uid']] = 1;
            }
            $v['uid'] > 0 && $this->_feed($v['articleid']); //feed
        }
        $q->fetch_array();
        if($catids) {
            foreach($catids as $catid => $num) {
                $this->category_num_add($catid, $num);
            }
        }
        if($uids) {
            foreach($uids as $uid => $num) {
                $this->user_point_add($uid, $num);
            }
        }
        if($artids) {
            $this->db->from($this->table);
            $this->db->set('status',1);
            $this->db->where_in('articleid',$artids);
            $this->db->update();
        }
    }

    //删除文章
    function delete($ids,$up_point=false) {
        $ids = parent::get_keyids($ids);
        $this->_delete(array('articleid'=>$ids), TRUE, $up_point);
    }

    //删除某些分类的文章
    function delete_catids($catids) {
        if(!$catids) return;
        $this->_delete(array('catid'=>$catids), FALSE, FALSE);
    }

    //删除某些主题的文章
    function delete_sids($sids) {
        if(empty($sids)) return;
        $where = array('sid'=>$sids);
        $this->_delete($where);
    }

    function _delete($where, $up_total = TRUE, $up_point = FALSE) {
        $this->db->from($this->table);
        $this->db->where($where);
        $this->db->select('articleid,sid,uid,status,catid,thumb,picture');
        if(!$q=$this->db->get()) return;
        if(!$this->in_admin) {
            $S =& $this->loader->model('item:subject');
            $mysubjects = $S->mysubject($this->global['user']->uid);
        }
        $artids = $catids = $uids = array();
        while($v=$q->fetch_array()) {
            //权限判断
            $access = $this->in_admin || $this->check_delete_access($v['uid'],$v['sid'],$mysubjects);
            if(!$access) redirect('global_op_access');
            $artids[] = $v['articleid'];
            @unlink(MUDDER_ROOT.$v['thumb']);
            @unlink(MUDDER_ROOT.$v['picture']);
            if($v['status']=='1') {
                if($up_total) {
                    if(isset($catids[$v['catid']])) {
                        $catids[$v['catid']]++;
                    } else {
                        $catids[$v['catid']] = 1;
                    }
                }
                if(!$up_point||!$v['uid']) continue;
                if(isset($uids[$v['uid']])) {
                    $uids[$v['uid']]++;
                } else {
                    $uids[$v['uid']] = 1;
                }
            }
        }
        if($up_total && $catids) {
            foreach($catids as $catid => $num) {
                $this->category_num_dec($catid, $num);
            }
        }
        if($uids) {
            foreach($uids as $uid => $num) {
                $this->user_point_dec($uid, $num);
            }
        }
        if($artids) {
            parent::delete($artids);
            $this->_delete_fields($artids);
            $this->_delete_comments($artids);
        }
    }

    // 排序
    function listorder($post) {
        if(!$post && !is_array($post)) redirect('global_op_unselect');
        foreach($post as $id => $val) {
            $listorder = (int) $val['listorder'];
            $this->db->from($this->table);
            $this->db->set('listorder',$listorder);
            $this->db->where('articleid',$id);
            $this->db->update();
        }
    }

    // 更新att
    function upatt($ids,$att) {
        $ids = parent::get_keyids($ids);
        $this->db->from($this->table);
        $this->db->set('att',$att);
        $this->db->where_in('articleid',$ids);
        $this->db->update();
    }

    // 更新浏览量
    function pageview($articleid, $num=1) {
        $num = intval($num);
        if(empty($num)) return;
        $this->db->from($this->table);
        $this->db->set_add('pageview', $num);
        $this->db->where('articleid', $articleid);
        $this->db->update();
    }

    //上传图片
    function upload_thumb(& $img) {
        $config = $this->variable('config');

        $thumb_w = $config['thumb_width'] ? $config['thumb_width'] : 200;
        $thumb_h = $config['thumb_height'] ? $config['thumb_height'] : 200;

        $img->set_max_size($this->global['cfg']['picture_upload_size']);
        $img->userWatermark = $this->global['cfg']['watermark'];
        $img->watermark_postion = $this->global['cfg']['watermark_postion'];
        $img->thumb_mod = $this->global['cfg']['picture_createthumb_mod'];
        //$img->limit_ext = array('jpg','png','gif');
        $img->set_ext($this->global['cfg']['picture_ext']);

        $img->set_thumb_level($this->global['cfg']['picture_createthumb_level']);
        $img->add_thumb('thumb', 's_', $thumb_w, $thumb_h);
        $img->upload('article');
    }

    //统计分类数量
    function total_cat_mun($catid) {
        $this->loader->helper('misc',$this->model_flag);
        $this->db->from($this->table);
        $this->db->where('catid',misc_article::category_catids($catid));
        return $this->db->count();
    }

    // 增加分类统计数量
    function category_num_add($catid, $num=1) {
        $this->db->from('dbpre_article_category');
        $this->db->set_add('total', $num);
        $this->db->where('catid', $catid);
        $this->db->update();
    }

    // 较少分类统计数量
    function category_num_dec($catid, $num=1) {
        $this->db->from('dbpre_article_category');
        $this->db->set_dec('total', $num);
        $this->db->where('catid', $catid);
        $this->db->update();
    }

    // 增加积分
    function user_point_add($uid, $num = 1) {
        if(!$uid) return;
        $P =& $this->loader->model('member:point');
        $BOOL = $P->update_point($uid, 'add_article', FALSE, $num);
        /*
        if(!$BOOL) return;
        $this->db->set_add('articles', $num);
        $this->db->update();
        */
    }

    // 减少积分
    function user_point_dec($uid, $num = 1) {
        if(!$uid) return;
        $P =& $this->loader->model('member:point');
        $BOOL = $P->update_point($uid, 'add_article', TRUE, $num);
        /*
        if(!$BOOL) return;
        $this->db->set_dec('articles', $num);
        $this->db->update();
        */
    }

    //会员组权限判断
    function check_access($key,$value,$jump) {
        if($this->in_admin) return TRUE;
        if($key=='article_post') {
            $value = (int) $value;
            if(!$value) {
                if(!$jump) return FALSE;
                if(!$this->global['user']->isLogin) redirect('member_not_login');
                redirect('article_access_disable');
            }
        } elseif($key=='article_delete') {
            $value = (int) $value;
            if(!$value) {
                if(!$jump) return FALSE;
                redirect('article_access_delete');
            }
        }
        return TRUE;
    }

    //判断2种角色的提交权限
    function check_post_access($op='add',$role='member',$sid,$uid) {
        if($this->in_admin) return TRUE;
        if($op=='add'||$op=='edit') {
            if(!$this->global['user']->check_access('article_post', $this, 0)) {
                !$sid && redirect('article_post_sid_empty');
                $S=&$this->loader->model('item:subject');
                return $S->is_mysubject($sid, $uid);
            } else {
                return true;
            }
        } else {
            if($role=='owner') {
                !$sid && redirect('article_post_sid_empty');
                $S=&$this->loader->model('item:subject');
                return $S->is_mysubject($sid, $uid);
            } elseif($this->global['user']->uid == $uid) {
                return TRUE;
            }
        }
        return false;
    }

    //判断删除权限
    function check_delete_access($uid,$sid,&$mysubjects) {
        if($this->in_admin) return true;
        if($sid>0 && in_array($sid,$mysubjects)) return true;
        if($uid>0 && $uid == $this->global['user']->uid && $this->global['user']->check_access('article_delete',$this,0)) return true;
        return false;
    }

    //统计
    function status_total($uid=null) {
        $this->db->from($this->table);
        $this->db->select('status');
        $this->db->select('*', 'count', 'COUNT( ? )');
        $uid && $this->db->where('uid',$uid);
        $this->db->group_by('status');
        if(!$q = $this->db->get())return array();
        $result = array();
        while($v=$q->fetch_array()) {
            $result[$v['status']] = $v['count'];
        }
        $q->free_result();
        return $result;
    }

    //顶一下
    function digg($id) {
        $f = _cookie('article_digg_'.$id) == '1';
        if($f) return false;
        $this->db->from($this->table);
        $this->db->set_add('digg', 1);
        $this->db->where('articleid',$id);
        $this->db->update();
        set_cookie('article_digg_'.$id, '1', 24*3600);
        return true;
    }

    //生成rss聚合
    function rss($catid) {
        $category = $this->variable('category');

        $this->loader->lib('rss',null,0);
        $RSS = new ms_rss();
        $RSS->title = $this->global['cfg']['sitename'] . '-' . $category[$catid]['name'];
        $RSS->link = url('article/list/catid/'.$catid,'',1);
        $RSS->description = lang('article_rss_des', array($this->global['cfg']['sitename'], $category[$catid]['name']));
        $RSS->copyright = lang('global_rss_copyright');

        $this->db->from($this->table);
        $this->db->select('articleid,subject,author,introduce,dateline');
        if($catid > 0) {
            $this->loader->helper('misc',$this->model_flag);
            $this->db->where_in('catid', misc_article::category_catids($catid));
        }
        $this->db->where('status',1);
        if($q = $this->db->get()) {
            while($v=$q->fetch_array()) {
                $RSS->add_item($v['subject'], url('article/detail/id/'.$v['articleid'],'',1), $v['author'], $v['introduce'], $v['dateline']);
            }
            $q->free_result();
        }

        $result = $RSS->create_xml();
        unset($RSS);
        return $result;
    }

    // 删除附表
    function _delete_fields($ids) {
        $this->db->from($this->field_table);
        $this->db->where_in('articleid',$ids);
        $this->db->delete();
    }

    // 删除文章评论表
    function _delete_comments($ids) {
        //删除评论
        if(check_module('comment')) {
            $CM =& $this->loader->model(':comment');
            $CM->delete_id('article', $ids);
        }
    }

    // feed
    function _feed($articleid) {
        if(!$articleid) return;

        $FEED =& $this->loader->model('member:feed');
        if(!$FEED->cfg['uc_feed']) return;
        $this->global['fullalways'] = TRUE;

        if(!$detail = $this->read($articleid)) return;
        if(!$detail['uid']) return;

        $feed = array();
        $feed['icon'] = lang('article_feed_icon');
        $feed['title_template'] = lang('article_feed_title_template');
        $feed['title_data'] = array (
            'username' => '<a href="'.url("space/index/uid/$detail[uid]").'">' . $detail['author'] . '</a>',
        );
        $feed['body_template'] = lang('article_feed_body_template');
        $feed['body_data'] = array (
            'subject' => '<a href="'.url("article/detail/id/$detail[articleid]").'">' . $detail['subject'] . '</a>',
        );
        $feed['body_general'] = trimmed_title(strip_tags(preg_replace("/\[.+?\]/is", '', $detail['introduce'])), 150);

        $FEED->add($feed['icon'], $detail['uid'], $detail['author'], $feed['title_template'], $feed['title_data'], $feed['body_template'], $feed['body_data'], $feed['body_general']);
    }

}
?>