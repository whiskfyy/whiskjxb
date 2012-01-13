<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

class msm_item_category extends ms_model {

    var $model_flag = 'item';
    var $table = 'dbpre_category';
    var $key = 'catid';

    function __construct() {
        parent::__construct();
		$this->init_field();
    }

    function msm_item_category() {
        $this->__construct();
    }

	function init_field() {
		$this->add_field('name,modelid,review_opt_gid,config,listorder,enabled');
		$this->add_field_fun('name', '_T');
		$this->add_field_fun('modelid,review_opt_gid,listorder,enabled', 'intval');
	}

    function & read($catid) {
        $cat = parent::read($catid);
        if(empty($cat['pid'])) {
            $cat['config'] = unserialize($cat['config']);
            empty($cat['config']) && $cat['config'] = array();
        }
        return $cat;
    }

    function & getlist($pid=0) {
        $this->db->select('catid,pid,level,name,total,listorder,enabled');
        $this->db->from($this->table);
        $this->db->where('pid', $pid);
        $this->db->order_by('listorder');
        $row = $this->db->get();

        $result = array();
        if($row) {
            while($value = $row->fetch_array()) {
                $result[$value['catid']] = $value;
            }
        }
        return $result;
    }

    function subCateCount($catid) {
        $this->db->where('pid', $catid);
        $this->db->from($this->table);
        return $this->db->count();
    }

    function add($post, $batch = false, $upcache = TRUE) {
        if($batch) {
            // 多个分类批量添加
            $names = explode("|", $post['name']);
            if(count($names) > 1) {
                foreach($names as $val) {
                    if(!$val) continue;
                    $post['name'] = $val;
                    $this->add($post, false, false);
                }
                if($upcache) $this->write_cache();
                return;
            }
        }

        $isroot = !isset($post['pid']) || $post['pid'] == 0;
        $post['config'] = $isroot ? serialize($this->_parse_config($post['config'])) : '';
        if($post['pid']) {
            if(!$cate = $this->read($post['pid'])) redirect('itemcp_cat_empty');
            $post['modelid'] = $cate['modelid'];
            $post['level'] = $cate['level'] + 1;
            //$this->get_parent_id($post['pid'],1);
        } else {
            $post['level'] = 1;
        }
        $id = parent::save($post,null,false);
        if($upcache) $this->write_cache();
        return $id;
    }

    function save($post, $catid=null, $up_cache=true) {
        if(empty($post['name'])) redirect('itemcp_cat_empty_name');
        if(empty($catid)) redirect(sprintf(lang('global_sql_keyid_invalid'),'catid'));

		if($catid) {
			$cat = $this->read($catid);
			if(!$cat) redirect('itemcp_cat_not_exist');

			$post['config'] = isset($post['pid']) ? '' : serialize($this->_parse_config($post['config']));
			if($cat['pid'] != $post['pid']) {
				// 更换了父类
			}
		}

        parent::save($post, $catid, $up_cache);
        if(!$up_cache) $this->write_cache();
    }

    function delete($catid) {
        $cat = $this->read($catid);
        if(empty($cat)) return;
        if(empty($cat['pid'])) {
            $this->db->where('pid', $catid);
            $this->db->from($this->table);
            if($this->db->count() > 0) {
                redirect('itemcp_cat_delete_item_exist');
            }
        } else {
            // 删除主题
            $S =& $this->loader->model('item:subject');
            $S->delete_catid($catid);
            unset($S);
        }
		if($cat['pid']>0) {
			//$this->update_subcat($cat['pid']);
		}
        parent::delete($catid);
        return $cat['pid'];
    }

	//更新父类列表内的可用和不可用的子分类列表
	function update_subcat($catid) {
		$array = $this->_get_subcats($catid);
		$subcats = $nonuse_subcats = '';
		if($array) foreach($array as $val) {
			if($val['enabled']) $subcats .= $val['catid'] . ',';
			if(!$val['enabled']) $nonuse_subcats .= $val['catid'] . ',';
		}
		if($subcats) $subcats = substr($subcats,0,-1);
		if($nonuse_subcats) $nonuse_subcats = substr($nonuse_subcats,0,-1);

		$this->db->from($this->table);
		$this->db->set('subcats', $subcats);
		$this->db->set('nonuse_subcats', $nonuse_subcats);
		$this->db->where('catid', $catid);
		$this->db->update();
	}

	//更新子分类列表
    function update_subcats($post,$pid=null) {
        if(empty($post)) return;
        foreach($post as $catid => $val) {
            $val['name'] = _T($val['name']);
            $val['listorder'] = (int) $val['listorder'];
            $val['enabled'] = (int) $val['enabled'];
            $this->db->from($this->table);
            $this->db->set($val);
            $this->db->where('catid',$catid);
            $this->db->update();
        }
		//if($pid) $this->update_subcat($pid);
        $this->write_cache();
    }

    function rebuild($catids) {
        $ids = $this->get_keyids($catids);
        $S =& $this->loader->model('item:subject');
        foreach($ids as $catid) {
            $total = $S->get_category_total($catid);

            $this->db->set('total', $total);
            $this->db->from($this->table);
            $this->db->where('catid', $catid);
            $this->db->update();
        }
    }

    function get_parent_id($catid, $get_level = 1) {
        if(!$rel = $this->variable('category_rel')) return false;
        if(!$rel[$catid]) return false;
        list($pid, $level) = explode(':', $rel[$catid]);
        if($level == $get_level) return $catid;
        if($level-1 == $get_level) return $pid;
        if($get_level < $level-1) {
            list($pid, $level) = explode(':', $rel[$pid]);
        }
        if($level-1 == $get_level) return $pid;
    }

    function add_total($catid, $num=1) {
        $num = abs(intval($num));
        if(empty($num)) return;
        $this->db->set_dec('total', $num);
        $this->db->from($this->table);
        $this->db->update();
    }

    function dec_total($catid, $num=1) {
        $this->db->set_add('total', $num);
        $this->db->from($this->table);
        $this->db->update();
    }

    function update($post) {
        if(!is_array($post)||empty($post)) return;
        foreach($post as $catid => $val) {
            $val['enabled'] = (int) $val['enabled'];
            $this->db->from($this->table);
            $this->db->set($val);
            $this->db->where('catid', $catid);
            $this->db->update();
        }
        $this->write_cache();
    }

    function write_cache() {

		$js_data = "";
		$js_levle = array(1=>'',2=>'',3=>'');

        $this->db->from($this->table);
        $this->db->order_by(array('level'=>'ASC','listorder'=>'ASC'));

        if($query = $this->db->get()) {

            $i = 0;
            $result = $file = $level2 = $level3 = $rel = false;

            while($val = $query->fetch_array()) {
				$js_data .= $val['catid'] . ':"' . $val['name'] . '",';
                $rel[$val['catid']] = $val['pid'] . ':' . $val['level'];
                if($val['level']=='1') { //城市
					$js_levle[1][]= $val['catid'];
                    if($val['config']) $val['config'] = unserialize($val['config']);
                    $file[$val['catid']][$val['catid']] = $val;
                    $result[$val['catid']] = $val;
                } elseif($val['level']=='2') {//区/县
					$js_levle[2][$val['pid']][] = $val['catid'];
                    $file[$val['pid']][$val['catid']] = $val;
                } elseif($val['level']=='3') {//街道
					$js_levle[3][$val['pid']][] = $val['catid'];
                    if($file) foreach($file as $pkey => $pval) {
                        if(isset($pval[$val['pid']])) {
                            $file[$pkey][$val['catid']] = $val;
                        }
                    }
                }
            }

            $query->free_result();

			$js_data = 'data:{' . substr($js_data, 0, -1) . '},level:[';
			if($js_levle[1]) {
				$js_data .= '{0:['.implode(',',$js_levle[1]).']},';
			} else {
				$js_data .= '{0:[0]},';
			}
			if($js_levle[2]) {
				$js_data .= '{';
				foreach($js_levle[2] as $k => $v) $js_data .= $k.':[' . implode(',', $v) . '],';
				$js_data = substr($js_data, 0, -1) . '},';
			} else {
				$js_data .= "{0:[0]},";
			}
			if($js_levle[3]) {
				$js_data .= '{';
				foreach($js_levle[3] as $k => $v) $js_data .= $k.':[' . implode(',', $v) . '],';
				$js_data = substr($js_data, 0, -1) . '}';
			} else {
				$js_data .= '{0:[0]}';
			}

			$js_data = 'var _item_cate = {' . $js_data . ']};';

            write_cache('category', arrayeval($result), $this->model_flag);
            write_cache('category_rel', arrayeval($rel), $this->model_flag);
            foreach($file as $key => $val) {
                write_cache('category_' . $key, arrayeval($val), $this->model_flag);
            }
            write_cache('item_category', $js_data, $this->model_flag, 'js');

            //更新js后，因为浏览器会缓存js文件，所以需要给js做一个更新标识，放在模块配置里面
            $C =& $this->loader->model('config');
            $C->save(array('jscache_flag'=>rand(1,1000)), 'item');
        }
    }

    function _parse_config($post) {
        return $post;
    }

	function _get_subcats($pid) {
		if(!$pid) return;
		$this->db->from($this->table);
		$this->db->where('pid',$pid);
		$this->db->order_by('listorder');
		if(!$q=$this->db->get()) return;
		$result = array();
		while($v=$q->fetch_array()) {
			$result[] = $v;
		}
		$q->free_result();
		return $result;
	}
}
?>