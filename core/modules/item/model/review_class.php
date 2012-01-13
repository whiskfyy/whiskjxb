<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$_G['loader']->model('item:itembase', FALSE);
class msm_item_review extends msm_item_itembase {

    var $table = 'dbpre_review';
    var $key = 'rid';

    function __construct() {
        parent::__construct();
        $this->key = 'rid';
        $this->init_field();
    }

    function msm_item_review() {
        $this->__construct();
    }

    function init_field() {
        parent::add_field('sid,sort1,sort2,sort3,sort4,sort5,sort6,sort7,sort8,price,enjoy,title,content,taggroup,pictures');
        parent::add_field_fun('sid,sort1,sort2,sort3,sort4,sort5,sort6,sort7,sort8,price,enjoy', 'intval');
        parent::add_field_fun('title', '_T');
        parent::add_field_fun('content', '_TA');
    }

    function & read($rid, $select = '*', $join_subject = FALSE) {
        if(!$rid) redirect(lang('global_sql_keyid_invalid', $this->key));
        if($join_subject) {
            $this->db->join($this->table, 'r.sid', $this->subject_table, 's.sid');
        } else {
            $this->db->from($this->table);
        }
        $this->db->where('rid', $rid);
        $this->db->select($select);
        $result = $this->db->get_one();

        return $result;
    }

    function find($select, $where, $orderby, $start, $offset, $total = TRUE, $join_subject = FALSE, $join_member = FALSE) {
        if($where['pid']||$where['name']) $join_subject = TRUE;
        if($join_subject) {
            $this->db->join($this->table, 'r.sid', $this->subject_table, 's.sid', 'LEFT JOIN');
        }
        if($join_member) {
            if($join_subject) {
                $this->db->join_together('r.uid', 'dbpre_members', 'm.uid', 'LEFT JOIN');
            } else {
                $this->db->join($this->table, 'r.uid', 'dbpre_members', 'm.uid', 'LEFT JOIN');
            }
        }
        if(!$join_subject && !$join_member) {
            $this->db->from($this->table, 'r');
        }
        if($where) foreach($where as $key => $val) {
            if(preg_match("/[a-z]\.posttime/i", $key)) {
                if($val < 10000) {
                    $this->db->where_more($key, $this->global['timestamp'] - $val * 24 * 3600);
                }
            } else {
                if(is_array($val)) {
                    $this->db->where_in($key, $val);
                } else {
                    $this->db->where($key, $val);
                }
            }
        }
        $result = array(0, '');
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

    //获取我的点评列表
    function myreviewed($uid, $start, $offset, $total = TRUE) {
        $this->db->join($this->table, 'r.sid', $this->subject_table, 's.sid', 'LEFT JOIN');
        $where = array();
        $this->db->where('r.uid',$uid);
        $this->db->where('r.status',1);
        $result = array(0, '');
        if($total) {
            if(!$result[0] = $this->db->count()) {
                return $result;
            }
            $this->db->sql_roll_back('from,where');
        }
        $this->db->select('r.sid,s.name,s.subname,s.pid,s.catid');
        $this->db->group_by('r.sid');
        $this->db->limit($start, $offset);
        $result[1] = $this->db->get();
        return $result;
    }

    //保存点评
    function save($post, $rid = null) {
        $is_edit = $rid > 0;
        if(!$this->in_admin) $post['ip'] = $this->global['ip'];
        $W =& $this->loader->model('word'); //过滤字串和检测敏感字

        $S =& $this->loader->model('item:subject');
        if(!$subject = $S->read($post['sid'], '*', false)) redirect('item_empty');

        if($is_edit) {
            if(!$detail = $this->read($rid)) {
                redirect('item_review_empty');
            }
            if(!$this->in_admin && $this->global['user']->uid != $detail['uid']) {
                redirect('global_op_access');
            }
            if(!$this->in_admin) {
                if(isset($post['status'])) unset($post['status']);
                $post['isupdate'] = 1; //点评更新操作标记
            }
        } else {
            if($subject['status'] != '1') redirect('item_review_status_invalid');
            //检测会员组权限
            if(!$this->in_admin) {
                $review_access = $this->review_access($subject);
                if($review_access['code'] != 1) {
                    $this->redirect_access($review_access);
                }
            }
            if($this->global['user']->isLogin) {
                $post['uid'] = $this->global['user']->uid;
                $post['username'] = $this->global['user']->username;
            } else {
                //游客点评
                $post['uid'] = 0;
                $post['username'] = '';
            }
        }
        $post['posttime'] = $this->global['timestamp'];
        //上传的图片的应用
        if(is_array($post['pictures']) && $post['pictures']) {
            $post['pictures'] = $this->get_pictures($post['pictures']);
        } else {
            $post['pictures'] = '';
        }

        $pid = $subject['pid']; //主分类
        $post['pcatid'] = $pid;
        $category = $this->variable('category');
        //$post['pid'] = $pid;

        $catcfg = $category[$pid]['config']; //主分类的参数配置
        if(!$this->in_admin && !$this->global['user']->isLogin && !$catcfg['guest_review']) {
            // 针对游客的判断
            $forward = $this->global['web']['reuri'] ? ($this->global['web']['url'] . $this->global['web']['reuri']) : url('modoer','',1);
            redirect('member_op_not_login', url('member/login/forward/'.base64_encode($forward)));
        }
        //标题以主题名称命名(未填写的情况下)
        if(!$post['title']) $post['title'] = $subject['name'].$subject['subname'];
        if(!$is_edit) {
            $post['status'] = $category[$pid]['config']['reviewcheck'] ? 0 : ($W->check($post['content']) ? 0 : 1);
            //告之 控制页面 当前的状态
            define('RETURN_EVENT_ID', $post['status'] ? 'global_op_succeed' : 'global_op_succeed_check');
        } elseif($is_edit && !$this->in_admin) {
            //去掉前台可能的状态设置
            if(isset($post['status'])) unset($post['status']);
        }
        $post['content'] = $W->filter($post['content']); //检测和过滤敏感词
        $this->check_post($pid, $post, !empty($rid)); //提交检测
        $modelid = $category[$pid]['modelid']; //获取分类关联的模型ID
        $model = $this->variable('model_' . $modelid); //获取模型配置

        //处理标签
        if(isset($post['taggroup'])) {
            $TAG =& $this->loader->model('item:tag');
            if($detail['taggroup']) {
                $detail['taggroup'] = unserialize($detail['taggroup']); //反序列化，替换成数组形式
            }
            //检测标签并返回符合的标签组
            if($taggroup = $TAG->check_post($category[$pid]['config']['taggroup'], $post['taggroup'], $is_edit)) {
                if(!$is_edit) { //创建点评的情况
                    if($post['status']) {
                        //状态正常，直接将数据写入TAG表，并返回标签数组键名是tagid的标签数组
                        $TAG->add_batch($post['sid'], $taggroup);
                    }
                } elseif($is_edit) { //编辑点评的情况
                    if($detail['status'] && (!isset($post['status']) || $post['status'])) {
                        //删除已经取消的标签，写入新增加的标签
                        $TAG->replace_batch($detail['sid'], $taggroup, $detail['taggroup']);
                    } elseif($detail['status'] && isset($post['status']) && !$post['status']) {
                        //回到未审核状态时，删除之前写入的标签
                        $TAG->delete_batch($detail['sid'], $detail['taggroup']);
                    } elseif(!$detail['status'] && $post['status']) {
                        //写入新标签，如同新建点评一般
                        $TAG->add_batch($post['sid'], $taggroup);
                    }
                }
                $post['taggroup'] = serialize($taggroup);//重新序列化标签数组
            } elseif($is_edit && $detail['taggroup']) {
                //如果是编辑状态下，同时没有合适的标签，则删除之前存在的标签
                $TAG->delete_batch($detail['sid'], $detail['taggroup']);
                $post['taggroup'] = '';
            } else {
                $post['taggroup'] = '';
            }
        }

        if($is_edit) {
            //不提交没有改动过的字段
            foreach($detail as $key => $val) {
                if($val == $post[$key]) {
                    unset($post[$key]);
                }
            }
            if($post['taggroup'] == serialize($detail['taggroup'])) {
                unset($post['taggroup']);
            }
            if(count($post) == 1 && isset($post['posttime'])) {
                unset($post['posttime']);
            }
            $post && parent::save($post, $rid, FALSE, FALSE);
        } else {
            if(!isset($post['taggroup'])) $post['taggroup'] = '';
            $rid = parent::save($post, null, FALSE, FALSE);
        }

        //更新分类统计
        if(!$is_edit && $post['status']) {
            $this->update_item_point($post['sid'], $pid);
            $this->add_user_point($post['uid']);
            $this->activity($post['uid'], $post['username']);
            $this->_feed($rid);
        } elseif($is_edit) {
            if(!$detail['status'] && $post['status']) {
                $this->update_item_point($detail['sid'], $pid);
                if(!$detail['isupdate']) {
                    $this->add_user_point($detail['uid']);
                    $this->activity($detail['uid'], $detail['username']);
                    $this->_feed($rid);
                }
            }
            if($detail['status'] && isset($post['status']) && $post['status']=='0') {
                $this->subject_total_dec($detail['sid'], 1); 
            }
            if($detail['status']) {
                $update_point = FALSE;
                //对比是否更改了分数，来判断是否需要更新主题的积分
                for($i = 1; $i <= 8; $i++) {
                    $sortflag = 'sort'. $i;
                    if(isset($post[$sortflag]) && $detail[$sortflag] != $post[$sortflag]) {
                        $update_point = true;
                    }
                }
                $update_point && $this->update_item_point($detail['sid'], $pid);
            }
        }
        return $rid;
    }

    function checkup($rids) {
        if(is_numeric($rids) && $rids > 0) $rids = array($rids);
        if(!$rids || !is_array($rids)) redirect('global_op_unselect');
        //$this->db->from($this->table);
        $this->db->join($this->table,'r.sid',$this->subject_table,'s.sid');
        $this->db->select('r.rid,r.sid,r.uid,r.username,r.status,r.taggroup,r.isupdate,s.name,s.pid');
        $this->db->where('r.status',0);
        if(!$query = $this->db->get()) return;
        $upids = $sids = array();
        $TAG =& $this->loader->model('item:tag');
        $S =& $this->loader->model('item:subject');
        while($val = $query->fetch_array()) {
            $upids[] = $val['rid'];
            //处理标签
            if($val['taggroup'] && $val['name'] && $val['pid']) {
                $category = $S->get_category($val['pid']);
                $taggroup = unserialize($val['taggroup']);
                $TAG->add_batch($val['sid'], $taggroup);
                /*
                $this->db->from($this->table);
                $this->db->set('taggroup', $tag ? serialize($tag) : '');
                $this->db->where('rid', $val['rid']);
                $this->db->update();
                */
            }
            //需要更新的主题
            if(!in_array($val['sid'], $sids)) $sids[] = $val['sid'];
            if(!$val['isupdate'] && $val['uid'] > 0) {
                $this->add_user_point($val['uid']);
                $this->activity($val['uid'], $val['username']);
                $this->_feed($val['rid']);
            }
        }
        $query->free_result();
        //更新点评状态
        $this->db->from($this->table);
        $this->db->set('status',1);
        $this->db->where_in('rid', $upids);
        $this->db->update();
        //更新主题分数和统计
        if($sids) foreach($sids as $id) {
            $this->update_item_point($id);
        }
    }

    function delete($rids, $update_total = TRUE, $delete_point = FALSE, $is_sid = FALSE) {
        if(!$delete_point && !$this->in_admin && !$is_sid) $delete_point = TRUE;

        if(empty($rids)) redirect('global_op_unselect');
        if(!is_array($rids)) $rids = array((int)$rids);

        $this->db->join($this->table,'r.sid',$this->subject_table,'s.sid');
        if($is_sid) {
            $this->db->where_in('r.sid', $rids);
        } else {
            $this->db->where_in('r.rid', $rids);
        }
        $this->db->select('r.rid,r.sid,r.uid,r.status,r.taggroup,s.name,s.pid');
        if(!$result = $this->db->get()) return;

        $uids = $delids = $upsubject = array();
        while($value = $result->fetch_array()) {
            if(!$this->in_admin && $this->global['user']->uid != $value['uid']) {
                redirect('global_op_access');
            }
            $delids[] = $value['rid'];
            if($value['status']) {
                if($update_total && !in_array($value['sid'], $upsubject)) {
                    $upsubject[] = $value['sid'];
                }
                if($value['uid'] && $delete_point) {
                    if(isset($uids[$value['uid']])) {
                        $uids[$value['uid']]++;
                    } else {
                        $uids[$value['uid']] = 1;
                    }
                }
                //删除标签
                if($update_total && $value['taggroup']) {
                    if($taggroup = @unserialize($value['taggroup'])) {
                        $TAG =& $this->loader->model($this->model_flag.':tag');
                        $TAG->delete_batch($value['sid'], $taggroup);
                    }
                }
            }
            //删除回应
            $this->db->from('dbpre_responds');
            $this->db->where('rid', $value['rid']);
            $this->db->delete();
        }

        //删除记录
        if($delids) {
            $this->db->from($this->table);
            $this->db->where_in('rid', $delids);
            $this->db->delete();
        }

        //删除用户的对应积分
        if($delete_point && $uids) {
            $P =& $this->loader->model('member:point');
            foreach($uids as $uid => $num) {
                $P->update_point($uid, 'add_review', TRUE, $num);
            }
        }

        //更新主题分数
        foreach($upsubject as $sid) {
            $this->update_item_point($sid);
        }
    }

    //更新点评表里主题分类
    function update_category($sids, $pid) {
        $sids = parent::get_keyids($sids);
        $this->db->from($this->table);
        $this->db->set('pcatid',$pid);
        $this->db->where_in('sid',$sids);
        $this->db->update();
    }

    //检测当前会员是否已经点评过
    function reviewed($sid, $return_count = false) {
        $this->db->from($this->table);
        $this->db->where('sid', $sid);
        if($this->global['user']->isLogin) {
            $this->db->where('uid', $this->global['user']->uid);
        } else {
            $this->db->where('uid', 0);
            $this->db->where('ip', $this->global['ip']);
        }
        if($return_count) {
            $this->db->select('*', 'num', 'COUNT(?)');
            $this->db->select('posttime');
            $this->db->group_by('rid');
            $this->db->order_by('posttime','DESC');
            return $this->db->get_one();
        }
        $this->db->select('rid');
        $rid = $this->db->get_value();
        return !$rid ? FALSE : $rid;
    }

    //点评前的必要检测，并返回主题信息
    function check_review_before($sid, $isedit = FALSE, $goto = '') {
        if(!$sid) redirect(lang('global_sql_keyid_invalid', 'sid'));
        $this->db->from($this->subject_table);
        $this->db->select('sid,pid,catid,name,subname,status');
        $this->db->where('sid', $sid);
        if(!$detail = $this->db->get_one()) redirect('item_empty');
        if($detail['status'] != '1') redirect('item_review_status_invalid');
        //检测是否登录
        if(!$isedit && $rid = $this->reviewed($sid)) {
            if($goto) {
                $url = str_replace('_rid_', $rid, $goto);
                location($url);
                exit;
            }
            redirect('item_reviewed');
        }
        return $detail;
    }

    //点评提交检测
    function check_post($pid, $post, $isedit) {
        $category = $this->variable('category');
        $modelid = $category[$pid]['modelid'];
        $rogid = $category[$pid]['review_opt_gid'];
        $model = $this->variable('model_' . $modelid);
        $opts = $this->variable('review_opt_' . $rogid);

        foreach($opts as $val) {
            $flag = $val['flag'];
            if(!isset($post[$flag]) || !is_numeric($post[$flag]) || $post[$flag] < 0 || $post[$flag] > 5) {
                redirect(lang('item_review_pot_invalid', $val['name']));
            }
            unset($post[$flag]);
        }

        if($category[$pid]['useprice']) {
            if($category[$pid]['useprice_required'] && (!isset($post['price']) || !is_numeric($post['price']))) {
                redirect(lang('item_review_price_empty', $category[$pid]['useprice_title']));
            } elseif(isset($post['price']) && !is_numeric($post['price'])) {
                redirect(lang('item_review_price_empty', $category[$pid]['useprice_title']));
            }
        }

        if(!isset($post['enjoy']) || !is_numeric($post['enjoy']) || $post[$flag] < 0 || $post[$flag] > 3) {
            redirect('item_review_enjoy_invalid');
        }

        $mod = _G('mod');
        if($mod['flag'] != $this->model_flag) {
            $mod = read_cache(MUDDER_CACHE . 'module_' . $this->model_flag . '.php');
        }
        if(!$post['content']) {
            redirect('item_review_content_empty');
        } elseif(strlen($post['content']) < $mod['review_min'] || strlen($post['content']) > $mod['review_max']) {
            redirect(lang('item_review_content_charlen', array($mod['review_min'], $mod['review_max'])));
        }

        //如果还存在没开启的点评项时，则报错
        foreach(array_keys($post) as $key) {
            if(preg_match("/^sort[0-9]+$/", $key)) {
                if(!empty($post[$key])) redirect('item_review_form_invalid');
            }
        }
    }

    //获取引用的图片，并返回格式化的文本
    function get_pictures($picids) {
        $this->db->from('dbpre_pictures');
        $this->db->where('picid', $picids);
        if(!$q = $this->db->get()) return '';
        $result = '';
        while($v=$q->fetch_array()) {
            $result[$v['picid']] = array('thumb'=>$v['thumb'],'picture'=>$v['filename']);
        }
        return serialize($result);
    }

    function subject_total_add($sid, $num=1) {
        $this->db->from($this->subject_table);
        $this->db->where('sid', $sid);
        $this->db->set_add('reviews', $num);
        $this->db->update();
    }

    function subject_total_dec($sid, $num=1) {
        $this->db->from($this->subject_table);
        $this->db->where('sid', $sid);
        $this->db->set_dec('reviews', $num);
        $this->db->update();
    }

    function add_user_point($uid, $num = 1) {
        if(!$uid) return;
        $P =& $this->loader->model('member:point');
        $BOOL = $P->update_point($uid, 'add_review', 0, $num, FALSE, FALSE);
        if(!$BOOL) return;
        $this->db->set_add('reviews', $num);
        $this->db->update();
    }

    function dec_user_point($uid, $num = 1) {
        if(!$uid) return;
        $P =& $this->loader->model('member:point');
        $BOOL = $P->update_point($uid, 'add_review', TRUE, $num, FALSE, FALSE);
        if(!$BOOL) return;
        $this->db->set_dec('reviews', $num);
        $this->db->update();
    }

    function update_item_point($sid, $pid = null, $exec = TRUE) {
        if(!$pid) {
            $S =& $this->loader->model($this->model_flag.':subject');
            if(!$detail = $S->read($sid,'sid,pid,catid,name,subname,reviews,avgprice,enjoy,sort1,sort2,sort3,sort4,sort5,sort6,sort7,sort8',0)) return;
            $pid = $detail['pid'];
        }

        $category = $this->variable('category');
        $config = $category[$pid]['config'];
        if(!$rogid = $category[$pid]['review_opt_gid']) return;
        if(!$opts = $this->variable('review_opt_' . $rogid)) return;

        $modcfg = $this->variable('config');
        $st = round(($modcfg['scoretype'] ? $modcfg['scoretype'] : 5) / 5); //显示分数制 5分，10分，百分
        $dl = empty($modcfg['decimalpoint']) || $modcfg['decimalpoint'] < 0 ? 0 : $modcfg['decimalpoint']; //小数点位数

        $this->db->from($this->table);
        $this->db->select('status', 'num', 'SUM( ? )');
        $this->db->select('enjoy', 'enjoy', 'SUM( ? )');
        $this->db->where('sid', $sid);
        $this->db->where('status', 1);

        $set = array(); // 需要更新的字段
        foreach($opts as $key => $val) {
            $flag = $val['flag'];
            $this->db->select($flag, $flag, 'SUM( ? )');
            $set[$flag] = 0;
        }
        $grade = $this->db->get_one();
        if(empty($grade['num'])) {
            $this->db->from($this->subject_table);
            $this->db->set(array('avgsort'=>0,'avgprice'=>0,'reviews'=>0,'enjoy'=>0,'sort1'=>0,'sort2'=>0,'sort3'=>0,'sort4'=>0,
                'sort5'=>0,'sort6'=>0,'sort7'=>0,'sort8'=>0));
            $this->db->where('sid', $sid);
            $this->db->update();
            return;
        }

        foreach($opts as $key => $val) {
            $flag = $val['flag'];
            $set[$flag] = round( ($grade[$flag] / $grade['num'] * $st), $dl);
            $set['avgsort'] += $set[$flag];
        }
        $set['avgsort'] = round(($set['avgsort'] / count($opts)), $dl);
        $set['reviews'] = (int)$grade['num'];
        $set['enjoy'] = (int)$grade['enjoy'];

        //平均价格字段
        if($config['useprice']) {
            $this->db->from($this->table);
            $this->db->select('price', 'price', 'ROUND(AVG( ? ))');
            $this->db->where('sid', $sid);
            $this->db->where('status', 1);
            $this->db->where_more('price', 1); //金额必须大于等于1

            $avgprice = intval($this->db->get_value());
            $set['avgprice'] = $avgprice;
        } else {
            $set['avgprice'] = 0;
        }

        if($detail) foreach($set as $key => $val) {
            if($val == $detail[$key]) {
                unset($set[$key]);
            }
        }
        if(!count($set)) return $set;

        if(!$exec) return $set;

        $this->db->from($this->subject_table);
        $this->db->set($set);
        $this->db->where('sid', $sid);
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
        $A->save($uid, $username, 0, 1);
    }

    //检测当前用户的点评权限
    //返回数组的值 
    // code | 1:正常 -1:权限不足 -2:分类未开启游客点评 -3:分类未开启重复点 -4:会员没有重复点评权限 -5:会员重复点评次数已满(extra:最大次数) -6:重复点评时间间隔未到(extra:时间间隔)
    function review_access($sid=null) {
        $result = array('code'=>1,'extra'=>'');
        if($this->in_admin) return $result;
        if(!$this->global['user']->check_access('item_reviews', $this, FALSE)) {
            $result['code'] = -1;
            return $result;
        }
        if(!$sid) return $result;
        $S =& $this->loader->model('item:subject');
        $subject = is_array($sid) ? $sid : $S->read($sid,'*',false);
        $sid = $subject['sid'];
        $count = $this->reviewed($sid, true); //单个主题点评数量
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
        if(!$this->global['user']->check_access('item_repeat_review', $this, FALSE)) {
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

    //提示权限信息
    function redirect_access($result) {
        $lang = lang('item_review_access_'.$result['code']);
        $lang = str_replace('{S}', $result['extra'], $lang);
        redirect($lang);
    }

    //权限判断
    function check_access($key,$value,$jump) {
        if($this->in_admin) return TRUE;
        if($key=='item_reviews') {
            $value = (int) $value;
            if($value && $value < 0) {
                if(!$jump) return FALSE;
                if(!$this->global['user']->isLogin) redirect('member_not_login');
                redirect('item_access_alow_review');
            }
            if($value && $value < $this->global['user']->reviews) {
                if(!$jump) return FALSE;
                redirect('item_access_reviews');
            }
        }
        return TRUE;
    }

    //feed
    function _feed($rid) {
        if(!$rid) return;

        $FEED =& $this->loader->model('member:feed');
        if(!$FEED->cfg['uc_feed']) return;
        $this->global['fullalways'] = TRUE;

        if(!$detail = $this->read($rid)) return;
        if(!$detail['uid']) return;

        $feed = array();
        $feed['icon'] = lang('item_review_feed_icon');
        $feed['title_template'] = lang('item_review_feed_title_template');
        $feed['title_data'] = array (
            'username' => '<a href="'.url("space/index/uid/$detail[uid]").'">' . $detail['username'] . '</a>',
        );
        $feed['body_template'] = lang('item_review_feed_body_template');
        $feed['body_data'] = array (
            'title' => '<a href="'.url("item/detail/id/$detail[sid]").'">'.$detail['title'].'</a>',
            'respond' => '<a href="'.url("item/detail/id/$detail[sid]",'review').'">'.lang('item_respond').'</a>',
        );
        $feed['body_general'] = trimmed_title(strip_tags(preg_replace("/\[.+?\]/is", '', $detail['content'])), 150);

        $FEED->add($feed['icon'], $detail['uid'], $detail['username'], $feed['title_template'], $feed['title_data'], $feed['body_template'], $feed['body_data'], $feed['body_general']);
    }
}
?>