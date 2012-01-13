<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$_G['loader']->model('item:itembase', FALSE);
class msm_item_subject extends msm_item_itembase {

    var $table = 'dbpre_subject';
    var $key = 'sid';
    
    var $field;
    var $category = '';
    var $model = '';
    var $modcfg = '';

    function __construct() {
        parent::__construct();
        $this->field =& $this->loader->model('item:field');
        !$this->modcfg && $this->modcfg = $this->variable('config','item');
    }

    function msm_item_subject() {
        $this->__construct();
    }

    function init_field() {
        //处理为在后台管理的默认字段
        parent::add_field('domain,aid,pid,catid,avgsort,sort1,sort2,sort3,sort4,sort5,sort6,sort7,sort8,avgprice,best,reviews,
		guestbooks,pictures,pageviews,level,finer,owner,cuid,creator,addtime,video,thumb,status,mappoint,description');
        parent::add_field_fun('aid,avgprice,best,reviews,guestbooks,pictures,pageviews,level,finer,cuid,addtime,status','intval');
        parent::add_field_fun('sort1,sort2,sort3,sort4,sort5,sort6,sort7,sort8,avgsort','floatval');
        parent::add_field_fun('domain,thumb', '_T');
    }

    // 取得主题信息
    function read($key, $select = '*', $read_field = TRUE, $is_domain = FALSE) {
        if(!$key) {
            redirect(lang('global_sql_keyid_invalid', 'sid'));
        }
        $result = '';
        $this->db->from($this->table);
        if($is_domain) {
            $this->db->where('domain', $key);
        } else {
            $this->db->where('sid', $key);
        }
        $this->db->select($select);
        if($read_field && $select != '*' && !strposex($read_field,'catid')) {
            $this->db->select('catid');
        }
        if(!$result = $this->db->get_one()) {
            return $result;
        }
        $sid = $result['sid'];
        //坐标字段处理
        if(!num_empty($result['map_lng']) && !num_empty($result['map_lat'])) {
            $result['mappoint'] = $result['map_lng'] . ',' . $result['map_lat'];
        } else {
            $result['mappoint'] = '';
        }
        $result_field = array();
        if($read_field) {
            $result_field = $this->read_field($sid, $result['catid']);
        }
        if($result_field) {
            $result = array_merge($result, $result_field);
        }
        return $result;
    }

    function read_random($where=null) {
        $this->db->from($this->table);
        if($where) $this->db->where($where);
        $this->db->where('status',1);
        $this->db->order_by('rand()');
        $this->db->limit(0,1);
        return $this->db->get_one();
    }

    // 取得附表数据
    function read_field($sid, $catid, $select = '*') {
        $this->get_category($catid);
        $this->get_model($this->category['modelid']);
        $table = 'dbpre_' . $this->model['tablename'];
        $this->db->from($table);
        $this->db->where('sid', $sid);
        $this->db->select($select);
        $result = $this->db->get_one();
        return $result;
    }

    // 筛选数据
    function find($select, $where, $orderby, $start, $offset, $total = TRUE, $field_catid = NULL, $atts = NULL) {
        if($field_catid) {
            $this->get_category($field_catid);
            $this->get_model($this->category['modelid']);
            $table = 'dbpre_' . $this->model['tablename'];
            $this->db->join($this->table, 's.sid', $table, 'sf.sid', 'LEFT JOIN');
        } else {
            $this->db->from($this->table, 's');
        }
        if($atts) {
            foreach($atts as $att_catid => $attid) {
                $this->db->where_exist("SELECT 1 FROM dbpre_subjectatt st WHERE s.sid=st.sid AND attid=$attid");
            }
        }
        if($where['aid']) {
            $area = $this->loader->variable('area_1');
            if(!isset($area[$where['aid']])) {
                unset($where['aid']);
            } else {
                $area_slg = $area[$where['aid']];
                $adis = array((int)$where['aid']);
                //条件是第二层时，需要把最后一层地区也集合
                if($area_slg['level'] == 2) {
                    foreach($area as $key => $val) {
                        if($val['pid'] == $where['aid']) {
                            $adis[] = $key;
                        }
                    }
                    $where['aid'] = $adis;
                }
                unset($area_slg, $adis);
            }
        }

        if($where['fn']) {
            $keyword = $where['fn'];
            unset($where['fn']);
        }
        $this->db->where($where);
        //isset($keyword) && $this->db->where_concat_like(array('name','subname'), '%'.$keyword.'%');

        $result = array(0,'');
        if($total) {
            if(!$result[0] = $this->db->count()) {
                return $result;
            }
            $this->db->sql_roll_back('from,where');
        }

        $this->db->select($select ? $select : '*');
        $this->db->order_by($orderby);
        if($offset > 0) $this->db->limit($start, $offset);
        $result[1] = $this->db->get();
        return $result;
    }

    // 搜索标签
    function find_tag($tagid, $where, $select, $orderby, $start, $offset, $total = TRUE) {
        $this->db->join('dbpre_tag_data', 'td.id', $this->table, 's.sid', 'LEFT JOIN');
        $this->db->where('tagid', $tagid);
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
        $this->db->limit($start, $offset);
        $result[1] = $this->db->get();
        return $result;
    }

    // 搜索图片
    function find_picture($where, $orderby, $start, $offset, $total = TRUE) {
        $this->db->from($this->table);
        if($where['aid']) {
            $area = $this->loader->variable('area_1');
            if(!isset($area[$where['aid']])) {
                unset($where['aid']);
            } else {
                $area_slg = $area[$where['aid']];
                $adis = array((int)$where['aid']);
                if($area_slg['level'] == 2) {
                    foreach($area as $key => $val) {
                        if($val['pid'] == $where['aid']) {
                            $adis[] = $key;
                        }
                    }
                    $where['aid'] = $adis;
                }
                unset($area_slg, $adis);
            }
        }

        if($where['fn']) {
            $keyword = $where['fn'];
            unset($where['fn']);
        }
        $this->db->where($where);
        $this->db->where_more('pictures', 1);

        $result = array(0,'');
        if($total) {
            if(!$result[0] = $this->db->count()) {
                return $result;
            }
            $this->db->sql_roll_back('from,where');
        }
        $this->db->select("sid,pid,catid,name,subname,pictures,thumb");
        $this->db->order_by($orderby);
        $this->db->limit($start, $offset);
        $result[1] = $this->db->get();
        return $result;
    }

    // 保存主题
    function save($post, $sid = null) {

        $main_fields = array('sid','domain','aid','pid','catid','name','subname','avgsort','sort1','sort2','sort3','sort4','sort5',
            'sort6','sort7','sort8','avgprice','best','reviews','guestbooks','pictures','pageviews','level','finer','owner','cuid','creator',
            'addtime','video','thumb','status','map_lng','map_lat','description');

        if($edit = $sid > 0) {
            if(!$detail = $this->read($sid)) {
                redirect('global_op_empty');
            }
            if(!$this->in_admin && $this->global['user']->username != $detail['owner']) {
                redirect('global_op_access');
            }
            define('EDIT_SID', $sid);
        }

        if(!isset($post['thumb'])) $post['thumb'] = '';

        $catid = $post['catid'];
        $this->get_category($catid);
        $this->get_model($this->category['modelid']);
        $table = 'dbpre_' . $this->model['tablename'];

        // 字段检测
        $modelid = $this->category['modelid'];
        $data = $this->field->validator($modelid, $post);
        $data['pid'] = $this->category['catid'];
        if(isset($post['domain'])) $data['domain'] = $post['domain'];
        if(isset($post['owner'])) $data['owner'] = $post['owner'];
        if(isset($post['thumb'])) $data['thumb'] = _T($post['thumb']);
        //检测
        $this->check_post($data, $edit, $sid);

        if(!$edit) {
            !$this->in_admin && $this->global['user']->check_access('item_subjects', $this);
            // 主题登记/创建者
            if($post['cuid'] && isset($post['creator'])) {
                $data['cuid'] = $post['cuid'];
                $data['creator'] = _T($post['creator']);
            }
            $data['addtime'] = $this->global['timestamp'];
        }

        if(!$edit || $data['name'].$data['subname'] != $detail['name'].$detail['subname']) {
            if($this->exists($data['name'], $data['subname'], $sid)) {
                redirect(lang('item_post_exists_item', $this->model['item_name']));
            }
        }

        // 防止前台修改状态
        if($edit && !$this->in_admin) {
            unset($data['status']);
        } elseif(!$edit && !$this->in_admin) {
            $data['status'] = $this->category['config']['itemcheck'] ? 0 : 1;
            $data['domain'] = '';
        }

        $field_post = $main_post = array();
        foreach($data as $keyname => $val) {
            if(in_array($keyname, $main_fields)) {
                $main_post[$keyname] = $data[$keyname];
            } else {
                $field_post[$keyname] = $data[$keyname];
            }
        }

        /*
        //查询管理员信息
        if($main_post['owner'] && $detail['owner'] != $main_post['owner']) {
            $this->db->select('uid,username');
            $this->db->from('dbpre_members');
            $this->db->where('username', $data['owner']);
            if(!$member = $this->db->get_one()) redirect('item_post_owner_invalid');
        }
        */

        //去掉虚拟字段
        $v_f = array('mappoint');
        foreach($v_f as $v) {
            if(isset($field_post[$v])) unset($field_post[$v]);
        }
		//隐藏字段
		$i_f = array('forumid');
        foreach($i_f as $v) {
            if(isset($post[$v])) $field_post[$v] = $post[$v];
        }

        if($edit) {
            //主表
            foreach($main_post as $key => $val) {
                if($val == $detail[$key]) unset($main_post[$key]);
            }
            if($main_post) {
                $this->db->from($this->table);
                $this->db->set($main_post);
                $this->db->where('sid', $sid);
                $this->db->update();
            }
            //附表
            foreach($field_post as $key => $val) {
                if($val == $detail[$key]) unset($field_post[$key]);
            }
            if($field_post) {
                $this->db->from($table);
                $this->db->set($field_post);
                $this->db->where('sid', $sid);
                $this->db->update();
            }
        } else {
            //主表
            $this->db->from($this->table);
            $this->db->set($main_post);
            $this->db->insert();
            $sid = $this->db->insert_id();
            //附表
            $this->db->from($table);
            $this->db->set('sid', $sid);
            $this->db->set($field_post);
            $this->db->insert();
        }

        if(!$edit) {
            define('RETURN_EVENT_ID', $main_post['status'] ? 'global_op_succeed' : 'global_op_succeed_check');
        } elseif($edit) {
            if($main_post['status'] || $detail['status']) define('RETURN_EVENT_ID', 'global_op_succeed');
            if(!$main_post['status'] || !$detail['status']) define('RETURN_EVENT_ID', 'global_op_succeed_check');
        }

        //设置管理员
        if(!$edit && isset($main_post['owner'])) {
            $this->set_owner($sid, $main_post['owner'], '', false, false);
        }

        //上传的图片
        if(!empty($_FILES['picture']['name'])) {
            $P =& $this->loader->model('item:picture');
            $P->save(array('sid'=>$sid), TRUE, TRUE);
        }

        define('ITEM_PID', $data['pid']);
        $catid = $data['catid'];
        //更新分类统计
        if(!$edit && $data['status'] == 1) {
            $this->category_num_add($catid, 1); //新入不需要审核+1
            $this->add_user_point($post['cuid']); //会员积分
            if($post['cuid']>0) {
                //$this->activity($post['cuid'], $post['creator']); //会员活跃度
                $this->_feed($sid);
            }
        } elseif($edit) {
            if(isset($data['catid']) && ($detail['catid'] != $data['catid'])) { //是否更换了分类
                if($detail['status'] == 1) $this->category_num_dec($detail['catid']); //原分类通过审核的数量-1
                if($data['status'] == 1) $this->category_num_add($data['catid']); //新分类数量+1
            } else {
                if($detail['status'] != 1 && $data['status'] == 1) {
                    $this->category_num_add($data['catid']); //通过审核+1
                    if($post['cuid'] > 0) {
                        $this->add_user_point($post['cuid']);
                        $this->_feed($sid);
                    }
                    //$this->activity($post['cuid'], $detail['creator']);
                } elseif($detail['status'] == 1 && isset($data['status']) && $data['status'] != 1) {
                    $this->category_num_dec($detail['catid']); //更改审核状态-1
                }
            }
        }


        if(!isset($detail)) $detail = array();
        //if(isset($data['status'])) $field_post['status'] = $data['status'];
        //标签需要后期处理
        $this->save_tags($table, $sid, $modelid, $data, $detail, $edit);
        //属性需要后期处理
        $this->save_atts($table, $sid, $modelid, $data, $detail, $edit);

        return $sid;
    }

    // 保存主题附表
    function save_field(&$post, $sid = null) {
    }

    // 单独处理标签
    function save_tags($table, $sid, $modelid, &$post, &$detail, $edit = false) {
        $fields = $this->variable('field_' . $modelid);
        $savedata = array();
        $TAG =& $this->loader->model('item:tag');
        foreach($fields as $val) {
            if($val['type'] != 'tag') continue;
            if(!isset($post[$val['fieldname']])) continue; //如果不存，则表示数据和就的相同，已经被注销
            if($newtags = $post[$val['fieldname']]) {
                is_string($newtags) && $newtags = unserialize($newtags);
            }
            if($oldtags = $detail[$val['fieldname']]) {
                is_string($oldtags) && $oldtags = unserialize($oldtags);
            }
            if(!$gruopid = $val['config']['groupid']) return;
            if(!$oldtags && !$newtags) continue;
            if(!$edit) {
                if($post['status'] == 1) if($newtags) $TAG->add($gruopid, $sid, $newtags); //新建
            } else {
                if($detail['status'] != 1 && $post['status'] == 1) {
                    if($newtags) $TAG->add($gruopid, $sid, $newtags); //新建
                } elseif($detail['status'] == 1 && $post['status'] == 1) {
                    if($oldtags && $newtags) $TAG->replace($gruopid, $sid, $newtags, $oldtags); //删除，替换与更新
                    if(!$oldtags && $newtags) $TAG->add($gruopid, $sid, $newtags); //新建
                    if($oldtags && !$newtags) $TAG->delete($gruopid, $sid, $oldtags); //删除
                } elseif($detail['status'] == 1 && isset($post['status']) && $post['status'] != 1) {
                    if($oldtags)  $TAG->delete($gruopid, $sid, $oldtags); //删除
                }
            }
        }
    }
	
    // 单独处理属性
    function save_atts($table, $sid, $modelid, &$post, &$detail, $edit = false) {
        $fields = $this->variable('field_' . $modelid);
        $savedata = array();
        $AD =& $this->loader->model('item:att_data');
        foreach($fields as $val) {
            if($val['type'] != 'att') continue;
            if(!isset($post[$val['fieldname']])) continue; //如果不存，则表示数据和就的相同，已经被注销
            $newatts = $post[$val['fieldname']];
            if(!$catid = $val['config']['catid']) continue;
            if(!$edit) {
                if($post['status'] == 1) if($newatts) $AD->add($catid, $sid, $newatts); //新建
            } else {
                $oldatts = $detail[$val['fieldname']];
                if($detail['status'] != 1 && $post['status'] == 1) {
                    if($newatts) $AD->add($catid, $sid, $newatts); //新建
                } elseif($detail['status'] == 1 && ($post['status'] == 1||!isset($post['status']))) {
                    if($oldatts && $newatts) $AD->replace($catid, $sid, $newatts, $oldatts); //删除，替换与更新
                    if(!$oldatts && $newatts) $AD->add($catid, $sid, $newatts); //新建
                    if($oldatts && !$newatts) $AD->delete($catid, $sid, $oldatts); //删除
                } elseif($detail['status'] == 1 && isset($post['status']) && $post['status'] != 1) {
                    if($oldatts)  $AD->delete($catid, $sid, $oldatts); //删除
                }
            }
        }
    }

    //在列表内提交更新,只涉及主表字段
    function update($post) {
        if(!is_array($post) || !$post) redirect('global_op_nothing');
        foreach($post as $sid => $val) {
            $this->db->from($this->table);
            $this->db->set($val);
            $this->db->where('sid',$sid);
            $this->db->update();
        }
    }

    //批量移动分类
    function move($sids, $catid) {
        $sids = parent::get_keyids($sids);
        if(!$catid) redirect('item_subject_move_catid_empty');

		$C =& $this->loader->model('item:category');
		$root_pid = $C->get_parent_id($catid);
		$category = $this->variable('category_'.$root_pid);

        if(!$mtcat = $category[$catid]) redirect('itemcp_cat_empty');
        if($mtcat['pid']==0) redirect('item_subject_move_catid_isroot');
        $pid = $mtcat['pid'];
        $this->db->from($this->table);
        $this->db->where_in('sid', $sids);
        $this->db->select('sid,pid,catid,status');
        if(!$q=$this->db->get()) return;
        $dec_cats = $moveids = array();
        $add_num = 0;
        while($v=$q->fetch()) {
            if($v['catid'] == $catid) continue;
            $moveids[] = $v['sid'];
            if($v['status'] = 1) {
                if(in_array($v['catid'], $dec_cats)) {
                    $dec_cats[$v['catid']]++;
                } else {
                    $dec_cats[$v['catid']]=1;
                }
                $add_num++;
            }
        }
        $q->free();
        //更新数据
        if($moveids) {
            $this->db->from($this->table);
            $this->db->where_in('sid', $moveids);
            $this->db->set('pid',$pid);
            $this->db->set('catid',$catid);
            $this->db->update();
            //更新点评表
            $R =& $this->loader->model('item:review');
            $R->update_category($moveids, $pid);
        }
        //更新分类统计
        if($add_num > 0) {
            $this->category_num_add($catid, $add_num);
        }
        foreach($dec_cats as $catid=>$num) {
            $this->category_num_dec($catid,$num);
        }
    }

    // 删除主题
    function delete($ids, $delete_point = FALSE) {
        $ids = $this->get_keyids($ids);
        $where = array('sid'=>$ids);
        $this->_delete($where);
    }

    // 删除某个分类的主题
    function delete_catid($catid) {
        if(!is_numeric($catid) || !$catid) return;
        $where = array();
        if($this->is_root_category($catid)) {
            //$where['pid'] = $catid;
            if($catids = $this->get_sub_catids($catid)) {
                $catids[] = $catid;
            } else {
                $catids = $catid;
            }
            $where['catid'] = $catids;
        } else {
            $where['catid'] = $catid;
        }
        if(!$where['catid']) return;
        $this->_delete($where, FALSE, FALSE);
    }

    // 审核主题
    function checkup($sids) {
        if(is_numeric($sids) && $sids > 0) $sids = array($sids);
        if(!is_array($sids)||count($sids)==0) redirect('global_op_unselect');
        $this->db->from($this->table);
        $this->db->where_in('sid', $sids);
        $this->db->where_not_equal('status', 1);
        $this->db->select('sid,status,cuid,pid,catid');
        if(!$row = $this->db->get()) return;
        $upids = $pids = $atts = array();
        while ($value = $row->fetch_array()) {
            $upids[] = $value['sid'];
            $this->category_num_add($value['catid']);
            if($value['cuid'] > 0) {
                $this->activity($value['cuid'], $value['creator']); //活跃度
                $this->add_user_point($value['cuid']); //会员积分
                $this->_feed($value['sid']); //feed
            }
            $pids[$value['pid']][] = $value['sid'];
            //属性组
            $fielddata = $this->read_field($value['sid'], $value['catid']);
            $fielddata = array_merge($fielddata,$value);
            $fielddata['status'] = 1;
            $fielddata['modelid'] = $this->model['modelid'];
            $fielddata['tablename'] = 'dbpre_' . $this->model['tablename'];
            $atts[] = $fielddata;
        }
        $row->free_result();
        if($upids) {
            $this->db->from($this->table);
            $this->db->set('status', 1);
            $this->db->where_in('sid', $upids);
            $this->db->update();
            //标签部分因为在附表，所以要通过主分类id，获取模型表明，查询后处理
            $this->checkup_tags($pids);
            //添加属性组
            $detail = array();
            if($atts) foreach($atts as $val) {
                $this->save_atts($val['tablename'], $val['sid'], $val['modelid'], $val, $detail, false);
            }
        }
    }

    //审核更新标签
    function checkup_tags($data) {
        if(!$data) return false;
        $fields = $usetag = array();
        $TAG =& $this->loader->model('item:tag');
        //参数data的结构是 [pid]=>array([sid],[sid])
        foreach($data as $pid => $sids) {
            if(!$pid) continue;
            $model = $this->get_model($pid, TRUE); //取分类关联模型
            $modelid = $model['modelid'];
            if(!isset($fields[$modelid])) $fields[$modelid] = $this->variable('field_'.$modelid); //取模型字段
            if(!$fields[$modelid]) continue;
            foreach($fields[$modelid] as $k=>$val) {
                if($val['type'] == 'tag') $usetag[$modelid][$k] = $val; //取标签字段
            }
            //判断是否使用标签
            if(!isset($usetag[$modelid]) || count($usetag[$modelid])==0) continue;
            $select = 'sid'; 
            $selects = array(); 
            foreach($usetag[$modelid] as $val) {
                $select .= ','.$val['fieldname']; //获取数据的字段列表，由sid和其他标签字段组成
                $selects[$val['fieldname']] = $val; //设置以标签字段名称为键名的数组内容
            }
            $table = 'dbpre_' . $model['tablename']; //查询的主题附表
            $this->db->from($table);
            $this->db->where_in('sid', $sids); //查询的sid
            $this->db->select($select);
            if(!$q=$this->db->get()) continue;
            //获得数据
            while($v=$q->fetch_array()) {
                //循环每条数据的各个字段
                foreach($v as $_k => $_v) {
                    if($_k == 'sid') continue;
                    if(!$_v || strlen($_v)<3) continue;
                    //从selects里利通自读名称索引到标签关联的标签组id
                    if(!$groupid = (int)$selects[$_k]['config']['groupid']) continue;
                    $_v = unserialize($_v);
                    $TAG->add($groupid, $v['sid'], $_v); //加入标签表
                }
            }
            $q->free_result();
        }
    }

    // 重建统计
    function rebuild($sids) {
        if(is_numeric($sids) && $sids > 0) $sids = array($sids);
        if(!is_array($sids)||count($sids)==0) redirect('global_op_unselect');
        foreach($sids as $sid) {
            if(!$detail = $this->read($sid,'*',FALSE)) continue;
            $set = array();
            //点评
            $this->db->from('dbpre_review');
            $this->db->where(array('idtype'=>'item_subject','id'=>$sid,'status'=>1));
            $set['reviews'] = $this->db->count();
            //图片
            $this->db->from('dbpre_pictures');
            $this->db->where(array('sid'=>$sid,'status'=>1));
            $set['pictures'] = $this->db->count();
            //留言
            $this->db->from('dbpre_guestbook');
            $this->db->where(array('sid'=>$sid,'status'=>1));
            $set['guestbooks'] = $this->db->count();
            //分数统计
            $R =& $this->loader->model(':review');
            $myset = $R->update_review_point('item_subject',$sid,$this->get_review_config($detail), FALSE);
            $myset && $set = array_merge($set, $myset);
            unset($R);
            //其他模块关联的HOOK
            foreach(array_keys($this->global['modules']) as $flag) {
                if($flag == $this->model_flag) continue;
                $file = MUDDER_MODULE . $flag . DS . 'inc' . DS . 'item_rebuild_hook.php';
                if(is_file($file)) {
                    if($myset = include $file) {
                        $set = array_merge($set, $myset);
                    }
                }
            }
            //去掉不需要更新的
            foreach($set as $key => $val) {
                if($detail[$key] == $val) {
                    unset($set[$key]);
                }
            }
            if(empty($set)) continue;
            $this->db->from($this->table);
            $this->db->set($set);
            $this->db->where('sid',$sid);
            $this->db->update();
        }
    }

    // 更新浏览量
    function pageview($sid, $num=1) {
        $num = intval($num);
        if(empty($num)) return;
        $this->db->from($this->table);
        $this->db->set_add('pageviews', $num);
        $this->db->where('sid', $sid);
        $this->db->update();
    }

    // 取得某分类下的点评对象数量
    function category_count($catid) {
        if(empty($catid)) return false;
        $this->db->from($this->table);
        $this->db->where('catid', $catid);
        return $this->db->count();
    }

    // 查询主题名称是否存在
    function exists($name, $subname, $without_sid = NULL) {
        $this->db->from($this->table);
        $this->db->where('name', $name);
        $this->db->where('subname', $subname);
        $without_sid > 0 && $this->db->where_not_equal('sid', $without_sid);
        return $this->db->count() > 0;
    }

    //设置坐标点
    function mappoint($sid,$mappoint) {
        if(!$mappoint) return;
        list($lng,$lat) = explode(',', $mappoint);
        $this->db->from($this->table);
        $this->db->set('map_lng',$lng);
        $this->db->set('map_lat',$lat);
        $this->db->where('sid',$sid);
        $this->db->update();
    }

    // 设定管理员
    function set_owner($sid, $username, $expirydate = '', $update_subject = true, $show_error = true, $is_username = true) {
        if(!$sid || !$username) {
            if(!$show_error) return;
            redirect(lang('global_sql_keyid_invalid','sid|username'));
        }
        $M =& $this->loader->model(':member');
        if(!$member = $M->read($username, $is_username)) {
            if(!$show_error) return;
            redirect('member_empty');
        }
        $uid = $member['uid'];
        if($expirydate) {
            $this->loader->helper('validate');
            if(!validate::is_date($expirydate)) {
                if(!$show_error) return;
                redirect('item_post_owner_expirydate_invalid');
            }
            if(!$expirydate = strtotime($expirydate)) {
                if(!$show_error) return;
                redirect('item_post_owner_expirydate_invalid');
            }
            if($expirydate < $this->global['timestamp']) {
                if(!$show_error) return;
                redirect('item_post_owner_expirydate_less');
            }
        } else {
            $expirydate = 0;
        }
        $this->db->from('dbpre_mysubject');
        $this->db->where('sid', $sid);
        if($detail = $this->db->get_one()) {
            if($detail['uid'] == $uid && $detail['expirydate'] == $expirydate) return;
            $this->db->sql_roll_back('from,where');
            $this->db->set('uid', $uid);
            $this->db->set('expirydate', $expirydate);
            $this->db->set('lasttime ', _G('timestamp'));
            $this->db->update();
        } else {
            $this->db->sql_roll_back('from');
            $this->db->set('sid', $sid);
            $this->db->set('uid', $uid);
            $this->db->set('expirydate', $expirydate);
            $this->db->set('lasttime ', _G('timestamp'));
            $this->db->insert();
        }
        if($update_subject) {
            //if($detail && $detail['uid'] == $uid) return;
            $this->db->from($this->table);
            $this->db->set('owner',$member['username']);
            $this->db->where('sid',$sid);
            $this->db->update();
        }
    }

    // 删除一个管理员
    function delete_owner($sid, $uid) {
        $this->db->join('dbpre_mysubject','ms.uid','dbpre_members','m.uid');
        $this->db->where('sid', $sid);
        $this->db->select('ms.*,m.username');
        $this->db->order_by('ms.uid');
        if(!$q = $this->db->get()) return;
        $up_username = array();
        $delete = false;
        while($v=$q->fetch_array()) {
            if($v['uid'] == $uid) {
                $delete = true;
            } else {
                $up_username[] = $v['username'];
            }
        }
        if(!$delete) return;
        $this->db->from('dbpre_mysubject');
        $this->db->where('sid', $sid);
        $this->db->where('uid', $uid);
        $this->db->delete();
        //更新主题字段
        $this->db->from('dbpre_subject');
        $this->db->where('sid', $sid);
        $this->db->set('owner', $up_username ? implode(',',$up_username) : '');
        $this->db->update();
    }
    // 我管理的主题
    function mysubject($uid) {
        if(isset($this->global['mysubjects'])) return $this->global['mysubjects'];
        $mindate = strtotime(date('Y-m-d',$this->global['timestamp']));
        $result = array();
        $this->db->from('dbpre_mysubject');
        $this->db->where('uid', $uid);
        if(!$query = $this->db->get()) return $result;
        $delete = array();
        $up_sid = array();
        while($val = $query->fetch_array()) {
            if($val['expirydate'] == 0 || $val['expirydate'] >= $mindate) {
                $result[] = $val['sid'];
            } else {
                $delete[] = $val['id'];
                $up_sid[] = $val['sid'];
            }
        }
        if($delete) {
            $this->db->from('dbpre_mysubject');
            $this->db->where_in('id', $delete);
            $this->db->delete();
            $this->update_owner($delete); //更新主题表
        }
        return $result;
    }

    // 更新主题会员表
    function update_owner($sids) {
        $this->db->join('dbpre_mysubject','ms.uid','dbpre_members','m.uid');
        $this->db->where_in('sid', $sids);
        $this->db->select('ms.*,m.username');
        $this->db->order_by('ms.uid');
        $result = array();
        if(!$query = $this->db->get()) return $result;
        while($val = $query->fetch_array()) {
            $result[$val['sid']][] = $v['username'];
        }
        $query->free_result();
        foreach($sids as $sid) {
            $this->db->from('dbpre_subject');
            $this->db->where('sid', $sid);
            $this->db->set('owner', isset($result[$sid]) ? implode(',',$result[$sid]) : '');
            $this->db->update();
        }
    }

    // 判断我的主题
    function is_mysubject($sid, $uid) {
		if(isset($this->global['mysubjects']) && $uid == $this->global['user']->uid) {
			return in_array($sid, $this->global['mysubjects']);
		}
		$this->db->from('dbpre_mysubject');
		$this->db->where('uid', $uid);
		$this->db->where('sid', $sid);
		return $this->db->count() >= 1;
    }
    // 读取某个主题的管理员
    function owners($sid) {
        $this->db->join('dbpre_mysubject','ms.uid','dbpre_members','m.uid');
        $this->db->where('sid', $sid);
        $this->db->select('ms.*,m.username,m.groupid');
        $this->db->order_by('ms.uid');
        $result = array();
        if(!$query = $this->db->get()) return $result;
        while($val = $query->fetch_array()) {
            $result[] = $val;
        }
        $query->free_result();
        return $result;
    }

    // 读取最近浏览
    function read_cookie($pid) {
        $key = 'subject_' . $pid;
        $ckitems = empty($this->global['cookie'][$key]) ? array() : unserialize(stripslashes($this->global['cookie'][$key]));
        foreach($ckitems as $key => $val) {
            $ckitems[$key] = _T($val);
        }
        return $ckitems;
    }

    // 写入最近浏览的
    function write_cookie(& $subject, $num = 10, $day = 3) {
        $ckitems = $this->read_cookie($subject['pid']);
        if(!in_array($subject['sid'], $ckitems)) {
            $result = array();
            $result[$subject['sid']] = $subject['name'] . $subject['subname'];
            if(!empty($ckitems)) {
                $i = 1;
                foreach($ckitems as $key => $val) {
                    if($i >= $num) break;
                    $result[$key] = $val;
                    $i++;
                }
            }
        }
        set_cookie('subject_' . $subject['pid'], serialize($result), 86400 * $day);
    }

    //读取单个列表样式主题
    function display_listfield(&$subject) {
        $modelid = $this->get_modelid($subject['pid']);

        $subject_field = array();
        $select = 's.sid,pid,catid,name,avgsort,sort1,sort2,sort3,sort4,sort5,sort6,sort7,sort8,best,pageviews,reviews,pictures,thumb';
        $select_arr = explode(',', $select);
        $fields = $this->variable('field_' . $modelid);
        foreach($fields as $val) {
            if(!$val['show_list']) continue;
            if(!in_array($val['fieldname'], $select_arr)) {
                $select .= ',' . $val['fieldname'];
            }
            if(!in_array($val['fieldname'], array('name','subname','mappoint','owner','status','templateid','listorder'))) {
                $subject_field[] = $val;
            }
        }
        unset($select, $select_arr, $val, $fields);
        //主图内容
        $IFD =& $this->loader->model('item:fielddetail');
        //样式设计
        $IFD->td_num = 1; //表只有1个td
        $IFD->class = "";
        $IFD->width = "";
        $IFD->align = "left";
        $result = '';
        foreach($subject_field as $_val) {
            $result .= $IFD->detail($_val, $subject[$_val['fieldname']], $subject['sid']);
        }
        return $result;
    }

    //读取单个详细样式主题
    function display_detailfield($subject) {
        $modelid = $this->get_modelid($subject['pid']);
        //生成表格内容
        $FD =& $this->loader->model($this->model_flag.':fielddetail');
        //样式设计
        $FD->class = 'key';
        $FD->width = '';
        $result = '';
        //载入字段信息
        $fields = $this->variable('field_' . $modelid);
        foreach($fields as $val) {
            if(in_array($val['fieldname'], array('mappoint','status','templateid','listorder','content'))) continue;
            if($val['show_detail']) $result .= $FD->detail($val, $subject[$val['fieldname']], $subject['sid']) . "\r\n";
        }
        return $result;
    }

    //生成表单
    function create_form($pid, $subject = null, $ff_prarms = array('class'=>'altbg1')) {
        $cate = $this->loader->variable('category', $this->model_flag);

        if(!$category = $cate[$pid]) redirect('item_cat_empty');
        if(!$fieldlist = $this->loader->variable('field_' . $category['modelid'], $this->model_flag)) redirect('item_field_empty');

        $FF =& $this->loader->model($this->model_flag.':fieldform');
        if($ff_prarms && is_array($ff_prarms)) {
            foreach($ff_prarms as $k => $v) {
                $FF->$k = $v;
            }
        }
        $content = '';
        foreach($fieldlist as $val) {
            if(!$this->in_admin && $val['isadminfield']) continue;
            if(defined('item_allownull_' . $val['fieldname'])) $val['allownull'] = 0;
            $content .= $FF->form($val, $subject?$subject[$val['fieldname']]:'', $subject != null);
        }

        return $content;
    }

    // 取得某个分类的数量
    function get_category_total($catid) {
        $this->db->where('catid', $catid);
        $this->db->where('status', 1);
        $this->db->from($this->table);
        return $this->db->count(); 
    }

    // 增加分类统计数量
    function category_num_add($catid, $num=1) {
        $this->db->from('dbpre_category');
        $this->db->set_add('total', $num);
        $this->db->where('catid', $catid);
        $this->db->update();
    }

    // 较少分类统计数量
    function category_num_dec($catid, $num=1) {
        $this->db->from('dbpre_category');
        $this->db->set_dec('total', $num);
        $this->db->where('catid', $catid);
        $this->db->update();
    }

    // 增加积分
    function add_user_point($uid, $num = 1) {
        if(!$uid) return;
        $P =& $this->loader->model('member:point');
        $BOOL = $P->update_point($uid, 'add_subject', FALSE, $num, FALSE, FALSE);
        if(!$BOOL) return;
        $this->db->set_add('subjects', $num);
        $this->db->update();
    }

    // 减少积分
    function dec_user_point($uid, $num = 1) {
        if(!$uid) return;
        $P =& $this->loader->model('member:point');
        $BOOL = $P->update_point($uid, 'add_subject', TRUE, $num, FALSE, FALSE);
        if(!$BOOL) return;
        $this->db->set_dec('subjects', $num);
        $this->db->update();
    }

    //活跃度
    function activity($uid,$username) {
        if(!$uid && !$username) return;
        $post = array();
        if(!$uid || !$username) {
            $this->db->from('dbpre_members');
            $this->db->select('uid,username');
            if($uid) $this->db->where('uid', $uid);
            if($username) $this->db->where('username', $username);
            if(!$res = $this->db->get_one()) return;
            $uid = $res['uid'];
            $username = $res['username'];
        }
        $A =& $this->loader->model($this->model_flag.':activity');
        $A->save($uid,$username,1,0);
    }

    //检测添加主题权限
    function check_access($key,$value,$jump) {
        if($this->in_admin) return TRUE;
        if($key=='item_subjects') {
            $value = (int) $value;
            if($value && $value < 0) {
                if(!$jump) return FALSE;
                if(!$this->global['user']->isLogin) redirect('member_not_login');
                redirect('item_access_alow_subject');
            }
            if($value && $value < $this->global['user']->reviews) {
                if(!$jump) return FALSE;
                redirect('item_access_reviews');
            }
        }
        return TRUE;
    }

    //检查提交数据
    function check_post(&$post,$edit,$sid) {
        if($post['domain']) {
            !$this->domain_check($post['domain']) and redirect('item_post_domain_invalid');
            $this->domain_exists($post['domain'],$sid) and redirect('item_post_domain_exists');
        }
    }

    //二级域名的合法性检测
    function domain_check($domain) {
        if(preg_match("/^[0-9]+$/i", $domain)) return false;
        if(!preg_match("/^[a-z0-9]{1,20}$/i", $domain)) return false;
        if(is_numeric($domain)) return false;
        if(in_array($domain, array_keys($this->global['modules']))) return false;
        $actlist = array('ajax','member','index','list','detail','pic','allpic','reviews','top','tag','rss','search',
            'cate','category');
        if(in_array($domain, $actlist)) return false;
        if($reserve = $this->modcfg['reserve_sldomain']) {
            $list = explode(',', $reserve);
            if(in_array($domain, $list)) return false;
        }
        return true;
    }

    //检测二级域名是否存在
    function domain_exists($domain,$exc_sid=null) {
        $this->db->from($this->table);
        $this->db->where('domain',$domain);
        if($exc_sid>0) $this->db->where_not_equal('sid', $exc_sid);
        return $this->db->count() >= 1;
    }

    //检测当前用户的点评权限
    //返回数组的值 
    // code | 1:正常 -1:权限不足 -2:分类未开启游客点评 -3:分类未开启重复点 -4:会员没有重复点评权限 -5:会员重复点评次数已满(extra:最大次数) -6:重复点评时间间隔未到(extra:时间间隔)
    function review_access($sid=null) {
        $result = array('code'=>1,'extra'=>'');
        if($this->in_admin) return $result;
        $R =& $this->loader->model(':review');
        if(!$this->global['user']->check_access('review_num', $R, FALSE)) {
            $result['code'] = -1;
            return $result;
        }
        $R =& $this->loader->model(':review');
        if(!$sid) return $result;
        $subject = is_array($sid) ? $sid : $this->read($sid,'*',false);
        $sid = $subject['sid'];
        $count = $R->reviewed('item_subject', $sid, true); //单个主题点评数量
        $category = $this->get_category($subject['pid']);
        if(!$category['config']['guest_review'] && !$this->global['user']->isLogin) {
            $result['code'] = -2;
            return $result; //未开启游客点评
        }
        if(!$count['num']) return $result; //第一次点评
        if(!$category['config']['repeat_review'] && $count > 0) {
            $result['code'] = -3;
            return $result; //未开启重复点评
        }
        if(!$this->global['user']->check_access('review_repeat', $this, FALSE)) {
            $result['code'] = -4;
            return $result; //会员组没有重复点评权限
        }
        $minnum = (int) $category['config']['repeat_review_num'];
        if($minnum && $minnum <= $count['num']) {
            $result['code'] = -5;
            $result['extra'] = $minnum;
            return $result; //点评次数超过了
        }
        $time = $this->global['timestamp'] - $count['posttime'];
        $mintime = (int) $category['config']['repeat_review_time'];
        if($mintime && ($mintime*60) >= $time) {
            $result['code'] = -6;
            $result['extra'] = $mintime;
            return $result; //重复点评时间间隔未到
        }
        return $result;
    }

    //获取主题的名称
    function get_subject($params) {
        //vp($params);
        return $params['name'] . ($params['subname']?('('.$params['subname'].')'):'');
    }

    //获取主题所属名称
    function get_city_id($params) {
        return $params['city_id'];
    }

    //获取主题的pid
    function get_pid($params) {
        return $params['pid'];
    }

    //获取主题所属分类的参数设置
    function get_review_config($params) {
        $category = $this->variable('category');
        $pid = $params['pid'];
        $config = array();
        $config = $category[$pid]['config'];
        $config['review_opt_gid'] = $category[$pid]['review_opt_gid'];
        return $config;
    }

    //获取点评主分类规则列表
    function get_review_category() {
        $category = $this->variable('category');
        $array = array();
        if($category) foreach($category as $val) {
            $array[$val['catid']] = $val['name'];
        }
        return $array;
    }

    //删除主题
    function _delete($where, $uptotal = TRUE, $delete_point = TRUE) {
        $this->db->select('sid,pid,catid,status,owner,creator');
        $this->db->from($this->table);
        $this->db->where($where);
        if(!$row = $this->db->get()) return;

        $upids = $creator = array();
        while ($value = $row->fetch_array()) {
            if(!$this->in_admin && $this->global['user']->username != $value['owner']) {
                redirect('global_op_access');
            }
            $upids[$value['catid']][] = $value['sid'];
            if($value['status'] == '1') {
                // 分类统计更新
                if($uptotal) $this->category_num_dec($value['catid']);
                // 积分
                if($delete_point && $value['cuid']) {
                    if(isset($creator[$value['cuid']])) {
                        $creator[$value['cuid']]++;
                    } else {
                        $creator[$value['cuid']] = 1;
                    }
                }
            }
        }
        $row->free_result();

        if($upids) {
            $delids = array();
            foreach($upids as $pid => $ids) {
                // 附表(因模型会生成不同的表)
                $this->_delete_field($ids, $pid);
                $delids = array_merge($delids, $ids);
            }
            // 主题
            $this->db->from($this->table);
            $this->db->where_in('sid', $delids);
            $this->db->delete();
            // 删除关联表
            $this->_delete_relate($delids);
        }

        if($delete_point && $creator) {
            // 减少积分
            $P =& $this->loader->model('member:point');
            foreach($creator as $uid => $num) {
                $P->update_point($uid, 'add_subject', TRUE, $num);
            }
        }
    }

    // 删除附表
    function _delete_field($ids, $pid) {
        $this->get_category($pid);
        $this->get_model($this->category['modelid']);
        $table = 'dbpre_' . $this->model['tablename'];
        //删除附表数据
        $this->db->from($table);
        $this->db->where_in('sid', $ids);
        $this->db->delete();
        //删除会员参与
        $MF =& $this->loader->model('member:membereffect');
        $MF->delete($ids, $this->model['tablename']);
    }

    // 删除关联表信息（直接关联）
    function _delete_relate($sids) {
        //主题管理员表
        $this->db->from('dbpre_mysubject');
        $this->db->where_in('sid', $sids);
        $this->db->delete();
        //点评
        $R =& $this->loader->model(':review');
        $R->delete_idtype('item_subject', $sids, FALSE, FALSE);
        //图片
        $P =& $this->loader->model('item:picture');
        $P->delete($sids, FALSE, FALSE, TRUE);
        //留言
        $GB =& $this->loader->model('item:guestbook');
        $GB->delete($sids, FALSE, FALSE, TRUE);
        //标签
        $TAG =& $this->loader->model('item:tag');
        $TAG->delete_ids($sids);
        //收藏
        $FAV =& $this->loader->model('item:favorite');
        $FAV->delete_sids($sids);
        unset($R,$P,$GB,$TAG,$FAV);
        //其他模块关联的删除操作HOOK
        foreach(array_keys($this->global['modules']) as $flag) {
            if($flag == $this->model_flag) continue;
            $file = MUDDER_MODULE . $flag . DS . 'inc' . DS . 'item_delete_hook.php';
            if(is_file($file)) {
                @include $file;
            }
        }
    }

    //feed
    function _feed($sid) {
        if(!$sid) return;

        $FEED =& $this->loader->model('member:feed');
        if(!$FEED->cfg['uc_feed']) return;
        $this->global['fullalways'] = TRUE;

        if(!$detail = $this->read($sid,'*', FALSE)) return;
        if(!$detail['cuid']) return;

        $feed = array();
        $feed['icon'] = lang('item_subject_feed_icon');
        $feed['title_template'] = lang('item_subject_feed_title_template');
        $feed['title_data'] = array (
            'username' => '<a href="'.url("space/index/uid/$detail[cuid]").'">' . $detail['creator'] . '</a>',
        );
        $feed['body_template'] = lang('item_subject_feed_body_template');
        $title = $detail['name'] . ($detail['subname'] ? "($detail[subname])" : '');
        $feed['body_data'] = array (
            'title' => '<a href="'.url("item/detail/id/$detail[sid]").'">'.$title.'</a>',
            'review' => '<a href="'.url("review/member/ac/add/type/item_subject/id/$detail[sid]").'">'.lang('item_review').'</a>',
        );
        $feed['body_general'] = '';

        $FEED->add($feed['icon'], $detail['cuid'], $detail['creator'], $feed['title_template'], $feed['title_data'], $feed['body_template'], $feed['body_data'], $feed['body_general']);

    }
}
?>