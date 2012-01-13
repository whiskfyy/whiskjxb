<?php
!defined('IN_MUDDER') && exit('Access Denied');
return array (
  1 => 
  array (
    'callid' => '1',
    'module' => 'member',
    'calltype' => 'sql',
    'name' => '会员_财富榜',
    'fun' => 'sql',
    'var' => 'mydata',
    'cachetime' => '1000',
    'expression' => 
    array (
      'from' => '{dbpre}members',
      'select' => 'uid,username,point,coin',
      'where' => 'coin>0',
      'other' => '',
      'orderby' => 'coin DESC',
      'limit' => '0,10',
      'cachetime' => '1000',
    ),
    'tplname' => 'member_coin_top',
    'empty_tplname' => 'empty_div',
    'closed' => '0',
    'hash' => '',
  ),
  2 => 
  array (
    'callid' => '2',
    'module' => 'item',
    'calltype' => 'sql',
    'name' => '主题_会员参与',
    'fun' => 'sql',
    'var' => 'mydata',
    'cachetime' => '1000',
    'expression' => 
    array (
      'from' => '{dbpre}membereffect_total mt LEFT JOIN {dbpre}subject s ON (mt.id=s.sid)',
      'select' => 'mt.{effect} as effect,s.sid,s.catid,s.name,s.subname',
      'where' => 'mt.idtype=\'{idtype}\' AND mt.{effect}>0',
      'other' => '',
      'orderby' => 'mt.{effect} DESC',
      'limit' => '0,10',
      'cachetime' => '1000',
    ),
    'tplname' => 'item_subject_effect_li',
    'empty_tplname' => 'empty_li',
    'closed' => '0',
    'hash' => '',
  ),
  3 => 
  array (
    'callid' => '3',
    'module' => 'item',
    'calltype' => 'sql',
    'name' => '主题_相关主题',
    'fun' => 'sql',
    'var' => 'mydata',
    'cachetime' => '1000',
    'expression' => 
    array (
      'from' => '{dbpre}subject',
      'select' => 'sid,pid,catid,name,subname,avgsort,pageviews,reviews',
      'where' => 'name=\'{name}\' and status=1 and sid!={sid}',
      'other' => '',
      'orderby' => 'addtime DESC',
      'limit' => '0,10',
      'cachetime' => '1000',
    ),
    'tplname' => 'item_subject_li',
    'empty_tplname' => 'empty_li',
    'closed' => '0',
    'hash' => '',
  ),
  4 => 
  array (
    'callid' => '4',
    'module' => 'item',
    'calltype' => 'sql',
    'name' => '主题_同类主题',
    'fun' => 'sql',
    'var' => 'mydata',
    'cachetime' => '1000',
    'expression' => 
    array (
      'from' => '{dbpre}subject',
      'select' => 'sid,pid,catid,name,subname,avgsort,pageviews,reviews',
      'where' => 'catid={catid} and status=1 and sid!={sid}',
      'other' => '',
      'orderby' => 'addtime DESC',
      'limit' => '0,10',
      'cachetime' => '1000',
    ),
    'tplname' => 'item_subject_li',
    'empty_tplname' => 'empty_li',
    'closed' => '0',
    'hash' => '',
  ),
  5 => 
  array (
    'callid' => '5',
    'module' => 'item',
    'calltype' => 'sql',
    'name' => '主题_附近主题',
    'fun' => 'sql',
    'var' => 'mydata',
    'cachetime' => '1000',
    'expression' => 
    array (
      'from' => '{dbpre}subject',
      'select' => 'sid,pid,catid,name,subname,avgsort,pageviews,reviews',
      'where' => 'aid={aid} and status=1 and sid!={sid}',
      'other' => '',
      'orderby' => 'addtime DESC',
      'limit' => '0,10',
      'cachetime' => '1000',
    ),
    'tplname' => 'item_subject_li',
    'empty_tplname' => 'empty_li',
    'closed' => '0',
    'hash' => '',
  ),
  6 => 
  array (
    'callid' => '6',
    'module' => 'item',
    'calltype' => 'sql',
    'name' => '首页_最新图片',
    'fun' => 'sql',
    'var' => 'mydata',
    'cachetime' => '1000',
    'expression' => 
    array (
      'from' => '{dbpre}pictures',
      'select' => 'picid,title,thumb,sid',
      'where' => 'status=1',
      'other' => '',
      'orderby' => 'addtime DESC',
      'limit' => '0,7',
      'cachetime' => '1000',
    ),
    'tplname' => 'index_newpic',
    'empty_tplname' => 'empty_div',
    'closed' => '0',
    'hash' => '',
  ),
  7 => 
  array (
    'callid' => '7',
    'module' => 'item',
    'calltype' => 'sql',
    'name' => '首页_推荐主题',
    'fun' => 'sql',
    'var' => 'mydata',
    'cachetime' => '1000',
    'expression' => 
    array (
      'from' => '{dbpre}subject',
      'select' => 'sid,aid,name,subname,avgsort,thumb,description',
      'where' => 'pid={pid} AND status=1 AND finer>0',
      'other' => '',
      'orderby' => 'finer DESC',
      'limit' => '0,8',
      'cachetime' => '1000',
    ),
    'tplname' => 'index_subject_finer',
    'empty_tplname' => 'empty_div',
    'closed' => '0',
    'hash' => '',
  ),
  8 => 
  array (
    'callid' => '8',
    'module' => 'item',
    'calltype' => 'sql',
    'name' => '首页_最新点评',
    'fun' => 'sql',
    'var' => 'mydata',
    'cachetime' => '1000',
    'expression' => 
    array (
      'from' => '{dbpre}review r LEFT JOIN {dbpre}subject s ON(r.sid=s.sid)',
      'select' => 'rid,r.sid,r.uid,r.username,r.status,r.sort1,r.sort2,r.sort3,r.sort4,r.sort5,r.sort6,r.sort7,r.sort8,r.price,r.enjoy,r.posttime,r.isupdate,r.flowers,r.responds,r.ip,r.title,r.content,s.name,s.subname,s.pid',
      'where' => 'r.status=1',
      'other' => '',
      'orderby' => 'r.posttime DESC',
      'limit' => '0,5',
      'cachetime' => '1000',
    ),
    'tplname' => 'index_review',
    'empty_tplname' => 'empty_div',
    'closed' => '0',
    'hash' => '',
  ),
  9 => 
  array (
    'callid' => '9',
    'module' => 'member',
    'calltype' => 'sql',
    'name' => '会员_点评专家',
    'fun' => 'sql',
    'var' => 'mydata',
    'cachetime' => '1000',
    'expression' => 
    array (
      'from' => '{dbpre}members',
      'select' => 'uid,username,reviews',
      'where' => 'reviews>0',
      'other' => '',
      'orderby' => 'reviews DESC',
      'limit' => '0,10',
      'cachetime' => '1000',
    ),
    'tplname' => 'member_review_top',
    'empty_tplname' => 'empty_div',
    'closed' => '0',
    'hash' => '',
  ),
  10 => 
  array (
    'callid' => '10',
    'module' => 'member',
    'calltype' => 'sql',
    'name' => '会员_活跃会员',
    'fun' => 'sql',
    'var' => 'mydata',
    'cachetime' => '1000',
    'expression' => 
    array (
      'from' => '{dbpre}activity',
      'select' => 'uid,username, reviews',
      'where' => 'dateline>=EXTRACT(YEAR_MONTH FROM (DATE_SUB(NOW(), INTERVAL 1 MONTH)))',
      'other' => '',
      'orderby' => 'reviews DESC',
      'limit' => '0,9',
      'cachetime' => '1000',
    ),
    'tplname' => 'member_face_list',
    'empty_tplname' => 'empty_div',
    'closed' => '0',
    'hash' => '',
  ),
  11 => 
  array (
    'callid' => '11',
    'module' => 'product',
    'calltype' => 'sql',
    'name' => '产品_主题产品',
    'fun' => 'sql',
    'var' => 'mydata',
    'cachetime' => '1000',
    'expression' => 
    array (
      'from' => '{dbpre}product',
      'select' => 'pid,catid,subject,grade,description,thumb,comments,pageview',
      'where' => 'sid={sid} AND status=1',
      'other' => '',
      'orderby' => 'grade DESC,comments DESC',
      'limit' => '0,10',
      'cachetime' => '1000',
    ),
    'tplname' => 'product_pic_li',
    'empty_tplname' => 'empty_li',
    'closed' => '0',
    'hash' => '',
  ),
); 
?>