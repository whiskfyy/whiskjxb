<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
class msm_area extends ms_model {

    var $table = 'dbpre_area';
    var $key = 'aid';

    function __construct() {
        parent::__construct();
    }

    function msm_area() {
        $this->__construct();
    }

    function & get_list($pid = 0) {
        $result = array();
        $this->db->from($this->table);
        $this->db->where('pid', $pid);
        $this->db->order_by('listorder');

        if(!$row = $this->db->get()) {
            return $result;
        }
        while($value = $row->fetch_array()) {
            $result[] = $value;
        }
        return $result;
    }

    function save($post, $aid = null) {
        $edit = $aid > 0;
        if(isset($post['pid'])) {
            if(!$post['pid']) {
                $post['level'] = 1;
            } else {
                if(!$parent = $this->read($post['pid'])) {
                    redirect('admincp_area_empty_pid');
                } elseif($parent['level'] == '3') {
                    redirect('admincp_area_level_max');
                }
                $post['level'] = (int)$parent['level'] + 1;
            }
        }
        $aid = parent::save($post, $aid);
    }

    function check_post(&$post, $edit = false) {
        if(!$post['name']) redirect('admincp_area_empty_name');
        if(isset($post['level']) && $post['level'] == 1 && !$post['mappoint']) {
            redirect('admincp_area_empty_mappoint');
        }
        if($post['mappoint'] && !preg_match("/^[a-z0-9\-\.]+,[a-z0-9\-\.]+$/i", $post['mappoint'])) {
            redirect('admincp_area_error_mappoint');
        }
    }

    function delete($id) {
        if(!$id) return;
        $this->db->from($this->table);
        $this->db->where('aid',$id);
        $detail = $this->db->get_one();
        if($detail['level']=='1') return;
        if($detail['level']=='3') {
            parent::delete(array($id));
            return;
        }
        $delids = array();
        $this->db->where('pid',$id);
        $this->db->from($this->table);
        if($q=$this->db->get()) {
            while($v=$q->fetch_array()) {
                $delids[] = $v['aid'];
            }
            $q->free_result();
        }
        $delids[] = $id;
        parent::delete($delids);
    }

    function listorder($post) {
        if(!is_array($post)) redirect('global_op_unselect');
        foreach($post as $aid => $val) {
            $this->db->from($this->table);
            $this->db->set('listorder', (int)$val['listorder']);
            $this->db->where('aid',$aid);
            $this->db->update();
        }
        $this->write_cache();
    }

    function get_sub_aids($aid) {
        if(!$rel = $this->variable('area_rel')) return false;
        if(!$rel[$aid]) return false;
        list($pid,$level) = explode(':', $rel[$aid]);
        if($level == 3) return $aid;
        if($level == 2) {
            $city_id = $pid;
        }
        $aids = array($aid);
        foreach($rel as $id => $val) {
            if($val == $aid.':3') {
                $aids[] = $id;
            }
        }
        return $aids;
    }

    function get_parent_aid($aid, $get_level=null) {
        if(!$rel = $this->variable('area_rel')) return false;
        if(!$rel[$aid]) return false;
        list($pid,$level) = explode(':', $rel[$aid]);
        if(!$get_level || $get_level==$level-1) return $pid;
        if($get_level > 2 || $get_level==$level) return $aid;
        if($l>=1) {
            list($id,) = explode(':', $rel[$pid]);
            if($l==1) return $id;
        }
        list($id,) = explode(':', $rel[$id]);
        return $id;
    }

    function write_cache() {
        $this->_write_cache();
        //js地区文件更新标识
        $C =& $this->loader->model('config');
        $C->save(array('jscache_flag_area'=>rand(1,1000)), 'modoer');
    }

    // 将分类写入缓存
    function _write_cache() {
        global $db, $dbpre;
        $content = '';
        $content .= "var area = new Array();\r\n";
        $content .= "area[1] = new Array();\r\n";
        $content .= "area[2] = new Array();\r\n";
        $content .= "area[3] = new Array();\r\n";

        $this->db->from($this->table);
        $this->db->order_by(array('level'=>'ASC','listorder'=>'ASC'));
        if($query = $this->db->get()) {

            $i = 0;
            $result = $file = $level2 = $level3 = $rel = false;

            while($val = $query->fetch_array()) {
                $rel[$val['aid']] = $val['pid'].':'.$val['level'];
                if($val['level']=='1') {
                    $content .= "area[1][".$i++."] = '{$val['aid']},{$val['name']}';\r\n";
                    $result[$val['aid']] = $val;
                } elseif($val['level']=='2') {
                    $level2[$val['pid']][] = $val['aid'] . ',' . $val['name'];
                    $file[$val['pid']][$val['aid']] = $val;
                } elseif($val['level']=='3') {
                    $level3[$val['pid']][] =  $val['aid'] . ',' . $val['name'];
                    if($file) foreach($file as $pkey => $pval) {
                        if(isset($pval[$val['pid']])) {
                            $file[$pkey][$val['aid']] = $val;
                        }
                    }
                }
            }

            write_cache('area', arrayeval($result), $this->model_flag);
            write_cache('area_rel', arrayeval($rel), $this->model_flag);
            foreach($file as $key => $val) {
                write_cache('area_' . $key, arrayeval($val), $this->model_flag);
            }

            if($level2) foreach($level2 as $key => $val) {
                $content .= "area[2][$key] = new Array();\r\n";
                foreach($val as $_key => $_val) {
                    $content .= "area[2][$key][$_key] = '$_val';\r\n";
                }
            }

            if($level3) foreach($level3 as $key => $val) {
                $content .= "area[3][$key] = new Array();\r\n";
                foreach($val as $_key => $_val) {
                    $content .= "area[3][$key][$_key] = '$_val';\r\n";
                }
            }
        }

        write_cache('area', $content, $this->model_flag, 'js');
    }
}
?>
