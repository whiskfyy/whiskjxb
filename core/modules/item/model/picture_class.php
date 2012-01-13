<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$_G['loader']->model('item:itembase', FALSE);
class msm_item_picture extends msm_item_itembase {

    var $table = 'dbpre_pictures';
	var $key = 'picid';

	function __construct() {
		parent::__construct();
		$this->model_flag = 'item';
		$this->init_field();
	}

    function msm_item_picture() {
        $this->__construct();
    }

	function init_field() {
		$this->add_field('sid,title,comments');
		$this->add_field_fun('sid', 'intval');
		$this->add_field_fun('title,comments', '_T');
	}

	function find($select, $where, $orderby, $start=0, $offset=0, $total = TRUE, $join_subject = FALSE) {
		if($where['pid']) $join_subject = TRUE;
		if($join_subject) {
			$this->db->join($this->table, 'p.sid', $this->subject_table, 's.sid');
		} else {
			$this->db->from($this->table,'p');
		}
		$this->db->where($where);

        $result = array(0,'');
        if($total) {
            if(!$result[0] = $this->db->count()) {
                return $result;
            }
            $this->db->sql_roll_back('from,where');
        }

		$this->db->select($select ? $select : '*');
        $this->db->order_by($orderby);
        if($offset>0) $this->db->limit($start, $offset);
        $result[1] = $this->db->get();
        return $result;
	}

	function save($post, $multi = FALSE, $setthumb = FALSE) {
        !$this->in_admin && $this->global['user']->check_access('item_pictures', $this);
		$subject = $this->check_post_before($post['sid'], FALSE, $setthumb);
		$pid = $subject['pid'];
        if($multi) {
            $post['title'] = $subject['sid'] . '_'.rand(10000,99999);
        } elseif($this->in_admin && $this->in_ajax) {
            $post['title'] = $subject['sid'] . '_'.rand(10000,99999);
        }
		$this->check_post($post);
        if($this->in_admin) {
            $post['uid'] = 0;
            $post['username'] = $this->global['admin']->adminname;
            $post['status'] = 1;
        } else {
            $post['uid'] = $this->global['user']->uid;
            $post['username'] = $this->global['user']->username;
            $category = $this->variable('category');
            $post['status'] = $category[$pid]['config']['picturecheck'] ? 0 : 1;
        }
        $post['addtime'] = $this->global['timestamp'];
		define('RETURN_EVENT_ID', $post['status'] ? 'global_op_succeed' : 'global_op_succeed_check');

		$this->loader->lib('upload_image', NULL, FALSE);
		$img = new ms_upload_image('picture', $this->global['cfg']['picture_ext']);
		$this->upload($img);

		$post['filename']   = str_replace(DS, '/', $img->path . '/' . $img->filename);
		$post['thumb']      = str_replace(DS, '/', $img->path . '/' . $img->thumb_filenames['thumb']['filename']);
		$post['width']      = $img->width;
		$post['height']     = $img->height;
		$post['size']       = $img->size;

		$post   = $this->convert_post($post);
		$picid  = parent::save($post);

		if($post['status']) {
			$this->subject_total_add($post['sid'], $post['thumb'], 1, $setthumb);
			$post['uid'] && $this->add_user_point($post['uid']);
		}

        if(_post('do') == 'review_upload') {
            return $this->return_review_pic($picid, $post['thumb'], $post['filename']);
        }

        return $picid;
	}

    function delete($picids, $update_total = TRUE, $delete_point = FALSE, $is_sid = FALSE) {
        if(!$delete_point && !$this->in_admin && !$is_sid) $delete_point = TRUE;

        if(!is_numeric($picids)) $picids = array($picids);
        if(!is_array($picids) || empty($picids)) redirect('global_op_unselect');

        $this->db->from($this->table);
        if($is_sid) {
            $this->db->where_in('sid', $picids);
        } else {
            $this->db->where_in('picid', $picids);
        }
		$this->db->select('picid,sid,uid,filename,thumb,status');
		if(!$result = $this->db->get()) return;

        if(!$this->in_admin) {
            $S =& $this->loader->model($this->model_flag.':subject');
            $mysubjects = $S->mysubject($this->global['user']->uid);
        }

		$uids = $deleteids = array();
		while($value = $result->fetch_array()) {
            //判断权限(后台管理员,留言会员以及主题管理员)
            if(!$this->in_admin && ($this->global['user']->uid != $value['uid'] && !in_array($value['sid'], $mysubjects))) {
                redirect('global_op_access');
            }
            $deleteids[] = $value['picid'];
			if($value['status']) {
				//更新主题图片统计
				$update_total && $this->subject_total_dec($value['sid']);
				//记录需要删除积分的用户和数量
				if($value['uid'] && $delete_point) {
					if(isset($uids[$value['uid']])) {
						$uids[$value['uid']]++;
					} else {
						$uids[$value['uid']] = 1;
					}
				}
			}
			//删除图片，防止误删目录，做了字符数量判断
			if(strlen($value['filename']) > 12) {
				@unlink(MUDDER_ROOT . $value['filename']);
			}
			if(strlen($value['thumb']) > 12) {
				@unlink(MUDDER_ROOT . $value['thumb']);
			}
		}
print_r($deleteids);
		//删除记录
        if($deleteids) {
            $this->db->from($this->table);
            $this->db->where_in('picid', $deleteids);
            $this->db->delete();
        }

		//删除用户的对应积分
		if($delete_point && $uids) {
			$P =& $this->loader->model('member:point');
			foreach($uids as $uid => $num) {
				$P->update_point($uid, 'add_picture', TRUE, $num);
			}
		}
	}

	function update($post) {
		if(empty($post)) redirect('global_op_unselect');
		foreach($post as $key => $val) {
			if(!$key || !is_numeric($key) || $key < 1) continue; 
			$post = $this->get_post($val);
			$this->check_post($post, TRUE);
			$post = $this->convert_post($post);
			$this->db->from($this->table);
			$this->db->set($post);
			$this->db->where('picid', $key);
			$this->db->update();
		}
	}

	function upload(& $img) {
		$config = $this->variable('config');

		$thumb_w = $config['pic_width'] ? $config['pic_width'] : 200;
		$thumb_h = $config['pic_height'] ? $config['pic_height'] : 150;

        $img->set_max_size($this->global['cfg']['picture_upload_size']);
        $img->userWatermark = $this->global['cfg']['watermark'];
        $img->watermark_postion = $this->global['cfg']['watermark_postion'];
        $img->thumb_mod = $this->global['cfg']['picture_createthumb_mod'];
        $img->set_watermark_text(lang('item_picture_wtext',array($this->global['cfg']['watermark_text'], $this->global['user']->username)));

        $img->set_thumb_level($this->global['cfg']['picture_createthumb_level']);
		//$img->limit_ext = array('jpg','png','gif');//picture_ext
        $img->set_ext($this->global['cfg']['picture_ext']);
        $img->userWatermark = (int)$this->global['cfg']['watermark'];

		$img->add_thumb('thumb', 'thumb_', $thumb_w, $thumb_h, 0);
		$img->upload('pictures');
	}

	function checkup($picids) {
		if(empty($picids)) redirect('global_op_unselect');
        if(!is_array($picids)) $picids = array((int)$picids);
		$this->db->select('picid,sid,status,uid,thumb');
        $this->db->from($this->table);
        $this->db->where_in('picid', $picids);
        $this->db->where('status', 0);
        if(!$row = $this->db->get()) return;
        $uids = $upids = array();
        $thumb = '';
        while ($value = $row->fetch_array()) {
			$upids[] = $value['picid'];
			//更新主题记录
            $this->subject_total_add($value['sid'], $value['thumb']);
			//记录需要增加积分的用户和数量
			if($value['uid']) {
				if(isset($uids[$value['uid']])) {
					$uids[$value['uid']]++;
				} else {
					$uids[$value['uid']] = 1;
				}
			}
        }
        $row->free_result();
		//更新记录
        if($upids) {
            $this->db->from($this->table);
            $this->db->set('status', 1);
            $this->db->where_in('picid', $upids);
            $this->db->update();
        }
		//增加用户积分
		if($uids) {
			$P =& $this->loader->model('member:point');
			foreach($uids as $uid => $num) {
				$P->update_point($uid, 'add_picture', 0, $num);
			}
		}
	}

	function check_post_before($sid, $isedit = FALSE, $setthumb = FALSE) {
		if(!$sid || !is_numeric($sid)) {
			redirect(lang('global_sql_keyid_invalid', 'sid'));
		}
		$this->db->from($this->subject_table);
		$this->db->select('sid,pid,catid,name,subname,status');
		$this->db->where('sid', $sid);
		if(!$detail = $this->db->get_one()) redirect('item_empty');
		if(!$setthumb && $detail['status'] != '1') redirect('item_picture_status_invalid');

		return $detail;
	}

	function check_post(& $post, $isedit = FALSE) {
		//sid,modelid,title,comments
		if(!$post['title']) redirect('item_picture_empty_title');
		if(strlen($post['title']) > 20) redirect(lang('item_picture_title_charlen', 20));
	}

    //返回点评上传图片需要的格式
    function return_review_pic($picid, $thumb, $picture) {
        return "{ picid:\"$picid\",thumb:\"$thumb\",picture:\"$picture\"}";
    }

    // 增加主题的图片数量统计，并尝试设置主题封面
	function subject_total_add($sid, $thumb='', $num=1, $isthumb=FALSE) {
        // 新上传的图片是否这是为封面
        $set_thumb = FALSE;
        if($thumb) {
            if($isthumb) {
                $set_thumb = TRUE;
            } else {
                $modcfg = $this->variable('config');
                if($modcfg['thumb'] == '1') $set_thumb = TRUE;
                if($modcfg['thumb'] == '2') {
                    $this->db->from($this->subject_table);
                    $this->db->select('thumb,pictures');
                    $this->db->where('sid', $sid);
                    if(!$subject = $this->db->get_one()) return;
                    // 没有设置或者图片不存在
                    if(!$subject['thumb'] || !is_file(MUDDER_ROOT . $subject['thumb'])) {
                        $set_thumb = TRUE;
                    }
                }
            }
        }
        //更新主题
		$this->db->from($this->subject_table);
		$this->db->where('sid', $sid);
        $this->db->set_add('pictures');
        $set_thumb && $this->db->set('thumb', $thumb);
		$this->db->update();
	}

	function subject_total_dec($sid, $num=1) {
        $this->db->from($this->subject_table);
        $this->db->set_dec('pictures');
        $this->db->where('sid', $sid);
        $this->db->update();
	}

	function add_user_point($uid, $num = 1) {
        if(!$uid) return;
        $P =& $this->loader->model('member:point');
        $BOOL = $P->update_point($uid, 'add_picture', FALSE, $num, FALSE, FALSE);
        if(!$BOOL) return;
        $this->db->set_add('pictures', $num);
        $this->db->update();
	}

	function dec_user_point($uid, $num = 1) {
        if(!$uid) return;
        $P =& $this->loader->model('member:point');
        $BOOL = $P->update_point($uid, 'add_picture', TRUE, $num, FALSE, FALSE);
        if(!$BOOL) return;
        $this->db->set_dec('pictures', $num);
        $this->db->update();
	}

    //检测图片上传权限
    function check_access($key,$value,$jump) {
        if($this->in_admin) return TRUE;
        if($key=='item_pictures') {
            $value = (int) $value;
            if($value && $value < 0) {
                if(!$jump) return FALSE;
                redirect('item_access_alow_picture');
            }
            if($value && $value < $this->global['user']->pictures) {
                if(!$jump) return FALSE;
                redirect('item_access_pictures');
            }
        }
        return TRUE;
    }
}
?>