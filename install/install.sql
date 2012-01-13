DROP TABLE IF EXISTS modoer_activity;
CREATE TABLE modoer_activity (
  aid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) NOT NULL DEFAULT '0',
  username varchar(16) NOT NULL DEFAULT '',
  reviews smallint(5) unsigned NOT NULL DEFAULT '0',
  subjects smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (aid),
  KEY reviews (reviews),
  KEY dateline (dateline)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_admin;
CREATE TABLE modoer_admin (
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  adminname varchar(24) NOT NULL DEFAULT '',
  password varchar(32) NOT NULL DEFAULT '',
  email varchar(60) NOT NULL DEFAULT '',
  admintype tinyint(3) NOT NULL DEFAULT '0',
  is_founder char(1) NOT NULL DEFAULT 'N',
  logintime int(10) NOT NULL DEFAULT '0',
  loginip varchar(20) NOT NULL DEFAULT '',
  logincount int(10) unsigned NOT NULL DEFAULT '0',
  mymodules text NOT NULL,
  closed tinyint(1) NOT NULL DEFAULT '0',
  validtime int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  UNIQUE KEY adminname (adminname)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_adminsessions;
CREATE TABLE modoer_adminsessions (
  adminid mediumint(8) unsigned NOT NULL DEFAULT '0',
  ip char(16) NOT NULL DEFAULT '',
  dateline int(10) NOT NULL DEFAULT '0',
  errorcount tinyint(1) NOT NULL DEFAULT '0'
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_adv_list;
CREATE TABLE modoer_adv_list (
  adid mediumint(8) unsigned NOT NULL auto_increment,
  apid smallint(5) unsigned NOT NULL default '0',
  city_id mediumint(8) unsigned NOT NULL default '0',
  adname varchar(60) NOT NULL default '',
  sort enum('word','flash','code','img') NOT NULL,
  begintime int(10) unsigned NOT NULL default '0',
  endtime int(10) unsigned NOT NULL default '0',
  config text NOT NULL,
  code text NOT NULL,
  attr char(10) NOT NULL default '',
  ader varchar(255) NOT NULL default '',
  adtel varchar(255) NOT NULL default '',
  ademail varchar(255) NOT NULL default '',
  enabled char(1) NOT NULL default 'Y',
  listorder smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (adid)
) TYPE=MyISAM;

INSERT INTO modoer_adv_list VALUES(1, 1, 0, 'Modoer2.0发布', 'img', 1289260800, 0, 'a:5:{s:9:"img_title";s:7:"Modoer2";s:7:"img_src";s:38:"/uploads/adv/2010-11/83_1289335626.jpg";s:9:"img_width";s:3:"708";s:10:"img_height";s:2:"75";s:8:"img_href";s:22:"http://www.modoer.com/";}', '<a href="http://www.modoer.com/" alt="Modoer2" target="_blank"><img src="/uploads/adv/2010-11/83_1289335626.jpg" width="708" height="75" /></a>', '', '', '', '', 'Y', 0);
INSERT INTO modoer_adv_list VALUES(2, 2, 0, 'Modoer2.0发布', 'img', 1289260800, 0, 'a:5:{s:9:"img_title";s:7:"Modoer2";s:7:"img_src";s:38:"/uploads/adv/2010-11/21_1289338977.jpg";s:9:"img_width";s:3:"958";s:10:"img_height";s:2:"75";s:8:"img_href";s:22:"http://www.modoer.com/";}', '<a href="http://www.modoer.com/" alt="Modoer2" target="_blank"><img src="/uploads/adv/2010-11/21_1289338977.jpg" width="958" height="75" /></a>', '', '', '', '', 'Y', 0);

DROP TABLE IF EXISTS modoer_adv_place;
CREATE TABLE modoer_adv_place (
  apid smallint(5) unsigned NOT NULL auto_increment,
  templateid smallint(5) unsigned NOT NULL default '0',
  name varchar(60) NOT NULL default '',
  des varchar(255) NOT NULL default '',
  template text NOT NULL,
  enabled char(1) NOT NULL default 'Y',
  PRIMARY KEY  (apid),
  UNIQUE KEY name (name)
) TYPE=MyISAM;

INSERT INTO modoer_adv_place VALUES ('1','0','首页_中部广告','首页推荐主题下方广告位','<div class=\"ix_foo\">\r\n{get:adv ad=getlist(apid/_APID_/cachetime/1000)}\r\n<div>$ad[code]</div>\r\n{getempty(ad)}\r\n<center>AD</center>\r\n{/get}\r\n</div>','Y');
INSERT INTO modoer_adv_place VALUES ('2','0','新闻首页_广告','新闻模块的首页中午长条图片广告','<div class=\"art_ix\">\r\n{get:adv ad=getlist(apid/_APID_/cachetime/1000)}\r\n<div>$ad[code]</div>\r\n{getempty(ad)}\r\n<center>AD</center>\r\n{/get}\r\n</div>','Y');
INSERT INTO modoer_adv_place VALUES ('3','0','主题内容页_点评间广告','在主题内容页坐下点评列表第二行加入的广告','<div style=\"padding-bottom:10px;border-bottom:1px dashed #ddd;margin-bottom:10px;\">\r\n{get:adv ad=getlist(apid/_APID_/cachetime/1000)}\r\n<div style=\"text-align:center;\">$ad[code]</div>\r\n{getempty(ad)}\r\n<center>AD</center>\r\n{/get}\r\n</div>','Y');
INSERT INTO modoer_adv_place VALUES ('4','0','主题列表页_列表间广告','在主题模块的列表页面，列表第二层加入一个广告','<div style=\"padding-bottom:5px;border-bottom:1px dashed #ddd;margin:5px 0;\">\r\n{get:adv ad=getlist(apid/_APID_/cachetime/1000)}\r\n<div style=\"text-align:center;\">$ad[code]</div>\r\n{getempty(ad)}\r\n<center>AD</center>\r\n{/get}\r\n</div>','Y');


DROP TABLE IF EXISTS modoer_announcements;
CREATE TABLE modoer_announcements (
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(200) NOT NULL DEFAULT '',
  orders smallint(5) NOT NULL DEFAULT '0',
  content mediumtext NOT NULL,
  author varchar(50) NOT NULL DEFAULT '',
  pageview int(10) NOT NULL DEFAULT '0',
  dateline int(10) NOT NULL DEFAULT '0',
  available tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_area;
CREATE TABLE modoer_area (
  aid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  pid mediumint(8) unsigned NOT NULL DEFAULT '0',
  name varchar(16) NOT NULL DEFAULT '',
  mappoint varchar(50) NOT NULL DEFAULT '',
  level tinyint(1) unsigned NOT NULL DEFAULT '0',
  listorder smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (aid),
  KEY pid (pid),
  KEY level (level)
) TYPE=MyISAM;

INSERT INTO modoer_area VALUES ('1','0','宁波市','121.565151,29.877309','1','0');
INSERT INTO modoer_area VALUES ('2','1','江东区','','2','0');
INSERT INTO modoer_area VALUES ('5','2','天伦广场','','3','0');
INSERT INTO modoer_area VALUES ('3','1','海曙区','','2','0');
INSERT INTO modoer_area VALUES ('6','3','东门口','','3','0');
INSERT INTO modoer_area VALUES ('4','1','江北区','','2','0');
INSERT INTO modoer_area VALUES ('7','4','老外滩','','3','0');

DROP TABLE IF EXISTS modoer_article_category;
CREATE TABLE modoer_article_category (
  catid smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  pid smallint(5) NOT NULL DEFAULT '0',
  name varchar(20) NOT NULL DEFAULT '',
  listorder smallint(5) NOT NULL DEFAULT '0',
  total mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (catid),
  KEY pid (pid)
) TYPE=MyISAM;

INSERT INTO modoer_article_category VALUES ('1','0','默认分类','0','0');
INSERT INTO modoer_article_category VALUES ('2','1','默认子分类','0','0');

DROP TABLE IF EXISTS modoer_article_data;
CREATE TABLE modoer_article_data (
  articleid mediumint(8) unsigned NOT NULL DEFAULT '0',
  content mediumtext NOT NULL,
  PRIMARY KEY (articleid)
) TYPE=MyISAM;

INSERT INTO modoer_article_data VALUES ('1','\r\n            \r\n            \r\n        \r\n        <div class=\"content\">\r\n            <h3>商铺功能</h3>\r\n            <ul><li>可建立多板块的点评，例如（餐饮，旅游，购物，娱乐，服务等）</li><li>每个板块可以分类，并按类别输出信息（如餐饮板块可以建立火锅，海鲜等，出行/旅游板块可以建立汽车，旅行社\r\n等）</li><li>商铺可以设置，商铺名称，分店名称，主营菜系，地址，电话，手机，店铺标签(Tag)，并可增加分店</li><li>商家可通过认领功能，来管理自己的点评</li><li>商铺自定义风格功能</li><li>会员可补充商铺信息</li><li>已有商铺可增加分店</li><li>商铺可以根据环境，产品或者其他补充图片集展示，图片支持缩略图，水印功能</li><li>可自定义设置商铺封面</li><li>所有会员的提交信息可自动提交和后台管理审核</li><li>自定义城市区域，可以精确到街道</li><li>地图标注和地图报错功能</li><li>商铺视频功能</li><li>会员去过，想去的互动</li></ul>\r\n            <h3>点评功能</h3>\r\n            <ul><li>商铺可以针对各个板块可以自定义点评项名称和评分项数量），喜欢程度，人均消费，消费感受，适合类型进行点评，\r\n会员并可推荐产品以及设置店铺Tag，其他会员可以对点评进行献花和回应，反馈，举报点评</li><li>会员并可推荐产品以及设置店铺 Tag</li><li>其他会员可以对点评进行赠送鲜花和回应，反馈</li><li>可举报点评</li></ul>\r\n            <h3>会员卡模块</h3>\r\n            <ul><li>可自定义设置会员卡名称</li><li>可设置会员卡在商铺的折扣或者优惠活动和备注说明</li><li>可设置推荐加盟商家</li></ul>\r\n            <h3>兑奖中心模块</h3>\r\n            <ul><li>会员可通过点评，登记，回应等一系列互动操作得到金币积分，利用这些积分可对话相应积分的奖品</li><li>后台可添加和设置奖品以及奖品说明</li></ul>\r\n            <h3>优惠券模块</h3>\r\n            <ul><li>会员可上传优惠券，可供其他会员下载打印优惠券</li><li>后台可设置优惠券审核</li><li>其他会员可投票是否优惠券是否有用</li></ul>\r\n            <h3>新闻咨询模块</h3>\r\n            <ul><li>发布新闻资讯</li><li>商家可发布店铺的咨询信息</li><li>其他会员可投票是否优惠券是否有用</li></ul>\r\n            <h3>排行榜功能</h3>\r\n            <ul><li>餐厅排行（最佳餐厅、口味最佳、环境最佳、服务最佳）</li><li>最新餐厅（近一周加入、近一月加入、近三月加入）</li></ul>\r\n            <h3>会员功能</h3>\r\n            <ul><li>会员短信功能</li><li>个人主页功能（可以设置、更改个人主页名称和风格）</li><li>好友设置功能</li><li>个人留言版功能</li><li>会员积分功能</li><li>会员鲜花功能</li><li>收藏夹功能</li><li>积分等级</li></ul>\r\n            <h3>模块功能</h3>\r\n            <ul><li>Modoer系统以模块为基础组成</li><li>可以Modoer为平台开发安装模块</li></ul>\r\n            <h3>高级数据调用</h3>\r\n            <ul><li>利用内置的函数和SQL调用方式调用数据</li><li>可设置每个调用的模板和空数据的模板</li><li>调用数据可设置缓存，较少系统数据库资源消耗</li><li>支持本地和JS方式调用数据</li><li>\r\n			<br /></li></ul>\r\n			<h3>插件功能</h3>\r\n            <ul><li>利用插件接口可丰富系统功能</li><li>集成提供多个插件（图片轮换，网站公告）</li></ul>\r\n			<h3>系统整合</h3>\r\n			<ul><li>万能整合API，可与任何PHP程序进行整合</li><li>整合UCenter（账户，短信，好友，积分兑换，Feed推送，个人空间跳转UCH）</li></ul>\r\n			<h3>其他功能</h3>\r\n			<ul><li>词语过滤可设置不同的过滤方式：阻止，替换，审核</li><li>菜单管理可自定义模板显示的菜单，不需要再修改模板</li><li>伪静态功能优化SEO</li></ul>\r\n        </div>');

DROP TABLE IF EXISTS modoer_articles;
CREATE TABLE modoer_articles (
  articleid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  catid smallint(5) unsigned NOT NULL DEFAULT '0',
  sid mediumint(8) NOT NULL DEFAULT '0',
  dateline int(10) NOT NULL DEFAULT '0',
  att tinyint(1) NOT NULL DEFAULT '0',
  listorder smallint(5) unsigned NOT NULL DEFAULT '0',
  havepic tinyint(1) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  author varchar(20) NOT NULL DEFAULT '',
  subject varchar(60) NOT NULL DEFAULT '',
  keywords varchar(100) NOT NULL DEFAULT '',
  pageview mediumint(8) unsigned NOT NULL DEFAULT '0',
  grade tinyint(1) unsigned NOT NULL DEFAULT '0',
  digg mediumint(8) NOT NULL DEFAULT '0',
  closed_comment tinyint(1) unsigned NOT NULL DEFAULT '0',
  comments mediumint(8) unsigned NOT NULL DEFAULT '0',
  copyfrom varchar(200) NOT NULL DEFAULT '',
  introduce varchar(255) NOT NULL DEFAULT '',
  thumb varchar(255) NOT NULL DEFAULT '',
  picture varchar(255) NOT NULL DEFAULT '',
  status tinyint(1) NOT NULL DEFAULT '1',
  checker varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (articleid),
  KEY sid (sid),
  KEY uid (uid)
) TYPE=MyISAM;

INSERT INTO modoer_articles VALUES ('1','2','0','1275267913','1','0','0','0','admin','Modoer 点评系统','','1','0','0','0','0','','Modoer 是一款以本地分享，多功能的点评网站管理系统。采用 PHP+MYSQL 开发设计，开放全部源代码。因具有非凡的访问速度和卓越的负载能力而深受国内外朋友的喜爱。','','','1','');

DROP TABLE IF EXISTS modoer_att_cat;
CREATE TABLE modoer_att_cat (
  catid mediumint(8) NOT NULL auto_increment,
  name varchar(60) NOT NULL default '',
  des varchar(255) NOT NULL default '',
  PRIMARY KEY  (catid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_att_list;
CREATE TABLE modoer_att_list (
  attid mediumint(8) unsigned NOT NULL auto_increment,
  catid mediumint(8) unsigned NOT NULL default '0',
  name varchar(20) NOT NULL default '',
  listorder smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (attid)
) TYPE=MyISAM;


DROP TABLE IF EXISTS modoer_bcastr;
CREATE TABLE modoer_bcastr (
  bcastr_id smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  groupname varchar(15) NOT NULL DEFAULT 'index',
  available tinyint(1) NOT NULL DEFAULT '1',
  itemtitle varchar(100) NOT NULL DEFAULT '',
  link varchar(255) NOT NULL DEFAULT '',
  item_url varchar(255) NOT NULL DEFAULT '',
  orders smallint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (bcastr_id),
  KEY groupname (groupname)
) TYPE=MyISAM;

INSERT INTO modoer_bcastr VALUES ('1','index','1','Modoer点评系统','uploads/bcastr/25_1275267815.jpg','http://www.modoer.com','1');

DROP TABLE IF EXISTS modoer_card_apply;
CREATE TABLE modoer_card_apply (
  applyid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(20) NOT NULL DEFAULT '',
  linkman varchar(20) NOT NULL DEFAULT '',
  tel varchar(20) NOT NULL DEFAULT '',
  mobile varchar(20) NOT NULL DEFAULT '',
  address varchar(255) NOT NULL DEFAULT '',
  postcode varchar(10) NOT NULL DEFAULT '',
  num smallint(5) unsigned NOT NULL DEFAULT '1',
  coin int(10) NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  comment text NOT NULL,
  checker varchar(30) NOT NULL DEFAULT '',
  checktime int(10) NOT NULL DEFAULT '0',
  checkmsg text NOT NULL,
  PRIMARY KEY (applyid),
  KEY uid (uid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_card_discounts;
CREATE TABLE modoer_card_discounts (
  sid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  cardsort enum('both','largess','discount') NOT NULL DEFAULT 'discount',
  discount decimal(4,1) NOT NULL DEFAULT '0.0',
  largess varchar(100) NOT NULL DEFAULT '',
  exception varchar(255) NOT NULL DEFAULT '',
  addtime int(10) unsigned NOT NULL DEFAULT '0',
  available tinyint(1) NOT NULL DEFAULT '1',
  finer tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (sid),
  KEY available (available),
  KEY finer (finer,available)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_att_cat;
CREATE TABLE modoer_att_cat (
	catid mediumint(8) NOT NULL auto_increment,
	name varchar(60) NOT NULL default '',
	des varchar(255) NOT NULL default '',
	PRIMARY KEY  (catid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_att_list;
CREATE TABLE modoer_att_list (
	attid mediumint(8) unsigned NOT NULL auto_increment,
	catid mediumint(8) unsigned NOT NULL default '0',
	name varchar(20) NOT NULL default '',
	listorder smallint(5) unsigned NOT NULL default '0',
	PRIMARY KEY  (attid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_category;
CREATE TABLE modoer_category (
  catid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  pid mediumint(8) NOT NULL DEFAULT '0',
  level tinyint(1) unsigned NOT NULL default '0',
  modelid smallint(5) NOT NULL DEFAULT '0',
  review_opt_gid smallint(5) unsigned NOT NULL default '0',
  name varchar(20) NOT NULL DEFAULT '',
  total int(10) unsigned NOT NULL DEFAULT '0',
  config text NOT NULL,
  listorder smallint(5) NOT NULL DEFAULT '0',
  enabled TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (catid)
) TYPE=MyISAM;

INSERT INTO modoer_category VALUES ('1','0','1','1','1','美食','0','a:22:{s:9:\"gusetbook\";s:1:\"1\";s:13:\"subject_apply\";s:1:\"1\";s:19:\"subject_apply_uppic\";s:1:\"1\";s:24:\"subject_apply_uppic_name\";s:12:\"营业执照\";s:8:\"useprice\";s:1:\"1\";s:17:\"useprice_required\";s:1:\"1\";s:14:\"useprice_title\";s:12:\"人均消费\";s:13:\"useprice_unit\";s:7:\"元/人\";s:9:\"useeffect\";s:1:\"1\";s:7:\"effect1\";s:6:\"去过\";s:7:\"effect2\";s:6:\"想去\";s:8:\"taggroup\";a:1:{i:0;s:1:\"1\";}s:9:\"itemcheck\";s:1:\"0\";s:11:\"reviewcheck\";s:1:\"0\";s:12:\"picturecheck\";s:1:\"0\";s:14:\"guestbookcheck\";s:1:\"0\";s:12:\"guest_review\";s:1:\"0\";s:10:\"templateid\";s:1:\"0\";s:9:\"listorder\";s:7:\"addtime\";s:15:\"product_modelid\";s:1:\"0\";s:13:\"meta_keywords\";s:18:\"餐饮，美食，\";s:16:\"meta_description\";s:18:\"点评餐饮美食\";}','0','1');
INSERT INTO modoer_category VALUES ('2','1','2','0','1','自助餐','0','','0','1');
INSERT INTO modoer_category VALUES ('3','1','2','0','1','海鲜','0','','0','1');
INSERT INTO modoer_category VALUES ('4','1','2','0','1','湘菜','0','','0','1');
INSERT INTO modoer_category VALUES ('5','1','2','0','1','西餐/披萨','0','','0','1');
INSERT INTO modoer_category VALUES ('6','1','2','0','1','江浙菜','0','','0','1');
INSERT INTO modoer_category VALUES ('7','1','2','0','1','粤港菜','0','','0','1');
INSERT INTO modoer_category VALUES ('8','1','2','0','1','日韩菜','0','','0','1');
INSERT INTO modoer_category VALUES ('9','1','2','0','1','咖啡馆','0','','0','1');
INSERT INTO modoer_category VALUES ('10','1','2','0','1','蛋糕/甜点','0','','0','1');
INSERT INTO modoer_category VALUES ('11','1','2','0','1','川菜/渝菜','0','','0','1');
INSERT INTO modoer_category VALUES ('12','1','2','0','1','小吃/粥/面/快餐','0','','0','1');
INSERT INTO modoer_category VALUES ('13','1','2','0','1','酒吧','0','','0','1');
INSERT INTO modoer_category VALUES ('14','1','2','0','1','火锅','0','','0','1');
INSERT INTO modoer_category VALUES ('15','1','2','0','1','东北菜','0','','0','1');

DROP TABLE IF EXISTS modoer_comment;
CREATE TABLE modoer_comment (
  cid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  idtype varchar(30) NOT NULL DEFAULT '',
  id mediumint(8) unsigned NOT NULL DEFAULT '0',
  grade tinyint(2) NOT NULL DEFAULT '0',
  effect1 int(10) unsigned NOT NULL DEFAULT '0',
  effect2 int(10) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(20) NOT NULL DEFAULT '',
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  title varchar(255) NOT NULL DEFAULT '',
  content text NOT NULL,
  ip varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (cid),
  KEY idtype (idtype,id),
  KEY uid (uid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_config;
CREATE TABLE modoer_config (
  variable varchar(32) NOT NULL DEFAULT '',
  value text NOT NULL,
  module varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (variable,module)
) TYPE=MyISAM;

INSERT INTO modoer_config VALUES ('point','a:10:{s:3:\"reg\";a:2:{s:5:\"point\";i:20;s:4:\"coin\";i:10;}s:11:\"add_subject\";a:2:{s:5:\"point\";i:15;s:4:\"coin\";i:8;}s:10:\"add_review\";a:2:{s:5:\"point\";i:10;s:4:\"coin\";i:5;}s:11:\"add_picture\";a:2:{s:5:\"point\";i:5;s:4:\"coin\";i:5;}s:13:\"add_guestbook\";a:2:{s:5:\"point\";i:5;s:4:\"coin\";i:6;}s:11:\"add_respond\";a:2:{s:5:\"point\";i:5;s:4:\"coin\";i:5;}s:14:\"update_subject\";a:2:{s:5:\"point\";i:5;s:4:\"coin\";i:8;}s:13:\"report_review\";a:2:{s:5:\"point\";i:5;s:4:\"coin\";i:5;}s:11:\"add_comment\";a:2:{s:5:\"point\";i:2;s:4:\"coin\";i:1;}s:11:\"add_article\";a:2:{s:5:\"point\";i:5;s:4:\"coin\";i:3;}}','member');
INSERT INTO modoer_config VALUES ('siteclose','0','modoer');
INSERT INTO modoer_config VALUES ('icpno','','modoer');
INSERT INTO modoer_config VALUES ('sitename','Modoer点评系统','modoer');
INSERT INTO modoer_config VALUES ('seccode','0','modoer');
INSERT INTO modoer_config VALUES ('useripaccess','','modoer');
INSERT INTO modoer_config VALUES ('adminipaccess','','modoer');
INSERT INTO modoer_config VALUES ('ban_ip','','modoer');
INSERT INTO modoer_config VALUES ('gzipcompress','0','modoer');
INSERT INTO modoer_config VALUES ('scriptinfo','1','modoer');
INSERT INTO modoer_config VALUES ('picture_upload_size','600','modoer');
INSERT INTO modoer_config VALUES ('watermark','1','modoer');
INSERT INTO modoer_config VALUES ('jstransfer','1','modoer');
INSERT INTO modoer_config VALUES ('jsaccess','','modoer');
INSERT INTO modoer_config VALUES ('googlesearch','0','modoer');
INSERT INTO modoer_config VALUES ('googlesearch_website','modoer.com','modoer');
INSERT INTO modoer_config VALUES ('tplext','.htm','modoer');
INSERT INTO modoer_config VALUES ('mapapi','http://api.51ditu.com/js/maps.js','modoer');
INSERT INTO modoer_config VALUES ('datacall_dir','./data/datacall','modoer');
INSERT INTO modoer_config VALUES ('datacall_clearinterval','1','modoer');
INSERT INTO modoer_config VALUES ('datacall_cleartime','1','modoer');
INSERT INTO modoer_config VALUES ('search_limit','60','modoer');
INSERT INTO modoer_config VALUES ('search_maxspm','20','modoer');
INSERT INTO modoer_config VALUES ('search_maxresults','500','modoer');
INSERT INTO modoer_config VALUES ('search_cachelife','3600','modoer');
INSERT INTO modoer_config VALUES ('rewrite','0','modoer');
INSERT INTO modoer_config VALUES ('rewritecompatible','1','modoer');
INSERT INTO modoer_config VALUES ('subname','多功能点评系统','modoer');
INSERT INTO modoer_config VALUES ('titlesplit',',','modoer');
INSERT INTO modoer_config VALUES ('meta_keywords','Meta Keywords','modoer');
INSERT INTO modoer_config VALUES ('meta_description','Meta Description','modoer');
INSERT INTO modoer_config VALUES ('headhtml','','modoer');
INSERT INTO modoer_config VALUES ('templateid','0','member');
INSERT INTO modoer_config VALUES ('editor_relativeurl','1','modoer');
INSERT INTO modoer_config VALUES ('page_cachetime','0','modoer');
INSERT INTO modoer_config VALUES ('console_menuid','3','modoer');
INSERT INTO modoer_config VALUES ('closereg','0','member');
INSERT INTO modoer_config VALUES ('censoruser','*admin*\r\n*管理员*','member');
INSERT INTO modoer_config VALUES ('existsemailreg','0','member');
INSERT INTO modoer_config VALUES ('salutatory','1','member');
INSERT INTO modoer_config VALUES ('salutatory_msg','尊敬的$username：\r\n\r\n欢迎您加入$sitename大家庭！\r\n祝你在$sitename过得愉快！\r\n\r\n$sitename运营团队\r\n$time','member');
INSERT INTO modoer_config VALUES ('showregrule','1','member');
INSERT INTO modoer_config VALUES ('regrule','这里填写新用户的注册协议！','member');
INSERT INTO modoer_config VALUES ('pic_width','200','item');
INSERT INTO modoer_config VALUES ('pic_height','150','item');
INSERT INTO modoer_config VALUES ('video_width','250','item');
INSERT INTO modoer_config VALUES ('video_height','200','item');
INSERT INTO modoer_config VALUES ('review_min','10','review');
INSERT INTO modoer_config VALUES ('review_max','1500','review');
INSERT INTO modoer_config VALUES ('respond_min','10','review');
INSERT INTO modoer_config VALUES ('respond_max','500','review');
INSERT INTO modoer_config VALUES ('avatar_review','0','review');
INSERT INTO modoer_config VALUES ('pcatid','9','item');
INSERT INTO modoer_config VALUES ('list_num','20','item');
INSERT INTO modoer_config VALUES ('review_num','5','review');
INSERT INTO modoer_config VALUES ('respond_num','5','review');
INSERT INTO modoer_config VALUES ('classorder','order','item');
INSERT INTO modoer_config VALUES ('thumb','2','item');
INSERT INTO modoer_config VALUES ('show_thumb','1','item');
INSERT INTO modoer_config VALUES ('show_thumb_sort','small','item');
INSERT INTO modoer_config VALUES ('mapapi_charset','','modoer');
INSERT INTO modoer_config VALUES ('main_menuid','1','modoer');
INSERT INTO modoer_config VALUES ('respondcheck','0','item');
INSERT INTO modoer_config VALUES ('pid','1','item');
INSERT INTO modoer_config VALUES ('closenote','正在升级，请稍后访问...','modoer');
INSERT INTO modoer_config VALUES ('gbook','1','space');
INSERT INTO modoer_config VALUES ('gbook_guest','1','space');
INSERT INTO modoer_config VALUES ('gbook_seccode','1','space');
INSERT INTO modoer_config VALUES ('templateid','0','space');
INSERT INTO modoer_config VALUES ('recordguest','1','space');
INSERT INTO modoer_config VALUES ('spacename','{username}的个人空间','space');
INSERT INTO modoer_config VALUES ('spacedescribe','读万卷书模，行万里路！','space');
INSERT INTO modoer_config VALUES ('index_reviews','5','space');
INSERT INTO modoer_config VALUES ('index_gbooks','5','space');
INSERT INTO modoer_config VALUES ('reviews','10','space');
INSERT INTO modoer_config VALUES ('gbooks','10','space');
INSERT INTO modoer_config VALUES ('seccode_review','0','review');
INSERT INTO modoer_config VALUES ('seccode_picupload','1','item');
INSERT INTO modoer_config VALUES ('seccode_guestbook','0','item');
INSERT INTO modoer_config VALUES ('seccode_respond','1','review');
INSERT INTO modoer_config VALUES ('templateid','1','modoer');
INSERT INTO modoer_config VALUES ('foot_menuid','66','modoer');
INSERT INTO modoer_config VALUES ('scoretype','10','review');
INSERT INTO modoer_config VALUES ('decimalpoint','2','review');
INSERT INTO modoer_config VALUES ('seccode_review_guest','1','review');
INSERT INTO modoer_config VALUES ('seccode_subject','0','item');
INSERT INTO modoer_config VALUES ('tag_split_sp','1','item');
INSERT INTO modoer_config VALUES ('menuid','80','space');
INSERT INTO modoer_config VALUES ('space_menuid','80','space');
INSERT INTO modoer_config VALUES ('multi_upload_pic','1','item');
INSERT INTO modoer_config VALUES ('multi_upload_pic_num','10','item');
INSERT INTO modoer_config VALUES ('console_seccode','0','modoer');
INSERT INTO modoer_config VALUES ('console_total','1','modoer');
INSERT INTO modoer_config VALUES ('ownernews','1','product');
INSERT INTO modoer_config VALUES ('ownernews_classid','1','product');
INSERT INTO modoer_config VALUES ('ownernews_check','0','product');
INSERT INTO modoer_config VALUES ('seccode_product','0','product');
INSERT INTO modoer_config VALUES ('check_product','1','product');
INSERT INTO modoer_config VALUES ('check_comment','0','product');
INSERT INTO modoer_config VALUES ('guest_post','0','comment');
INSERT INTO modoer_config VALUES ('member_seccode','0','comment');
INSERT INTO modoer_config VALUES ('guest_seccode','0','comment');
INSERT INTO modoer_config VALUES ('disable_comment','0','comment');
INSERT INTO modoer_config VALUES ('guest_comment','0','comment');
INSERT INTO modoer_config VALUES ('check_comment','0','comment');
INSERT INTO modoer_config VALUES ('post_comment','1','product');
INSERT INTO modoer_config VALUES ('filter_word','1','comment');
INSERT INTO modoer_config VALUES ('list_num','5','comment');
INSERT INTO modoer_config VALUES ('hidden_comment','0','comment');
INSERT INTO modoer_config VALUES ('comment_interval','5','comment');
INSERT INTO modoer_config VALUES ('mapflag','51ditu','modoer');
INSERT INTO modoer_config VALUES ('manage_comment','0','product');
INSERT INTO modoer_config VALUES ('seccode_reg','0','member');
INSERT INTO modoer_config VALUES ('seccode_login','0','member');
INSERT INTO modoer_config VALUES ('mail_debug','0','modoer');
INSERT INTO modoer_config VALUES ('ownernews','1','exchange');
INSERT INTO modoer_config VALUES ('ownernews_classid','1','exchange');
INSERT INTO modoer_config VALUES ('ownernews_check','0','exchange');
INSERT INTO modoer_config VALUES ('thumb_w','160','exchange');
INSERT INTO modoer_config VALUES ('thumb_h','100','exchange');
INSERT INTO modoer_config VALUES ('exchange_seccode','1','exchange');
INSERT INTO modoer_config VALUES ('keywords','礼品兑换,兑奖中心','exchange');
INSERT INTO modoer_config VALUES ('description','礼品兑换模块用户会员使用金币兑换网站提供的礼品','exchange');
INSERT INTO modoer_config VALUES ('picture_createthumb_level','80','modoer');
INSERT INTO modoer_config VALUES ('keywords','新闻模块','article');
INSERT INTO modoer_config VALUES ('description','文章信息，发布网站信息和主题资讯','article');
INSERT INTO modoer_config VALUES ('editor_image','1','article');
INSERT INTO modoer_config VALUES ('rss','1','article');
INSERT INTO modoer_config VALUES ('owner_post','1','article');
INSERT INTO modoer_config VALUES ('member_post','0','article');
INSERT INTO modoer_config VALUES ('post_check','1','article');
INSERT INTO modoer_config VALUES ('post_filter','1','article');
INSERT INTO modoer_config VALUES ('list_num','20','article');
INSERT INTO modoer_config VALUES ('owner_category','0','article');
INSERT INTO modoer_config VALUES ('member_category','0','article');
INSERT INTO modoer_config VALUES ('post_seccode','0','article');
INSERT INTO modoer_config VALUES ('member_bysubject','0','article');
INSERT INTO modoer_config VALUES ('meta_keywords','新闻模块','article');
INSERT INTO modoer_config VALUES ('meta_description','文章信息，发布网站信息和主题资讯','article');
INSERT INTO modoer_config VALUES ('post_comment','1','article');
INSERT INTO modoer_config VALUES ('att_custom','1|头条(默认显示2条)\r\n2|文字推荐(默认显示7条)\r\n3|图片推荐(默认显示3条)\r\n4|模块首页图片轮换(不宜过多)','article');
INSERT INTO modoer_config VALUES ('meta_keywords','兑奖中心','exchange');
INSERT INTO modoer_config VALUES ('meta_description','兑奖中心模块，用于消费金币','exchange');
INSERT INTO modoer_config VALUES ('map_view_level','2','modoer');
INSERT INTO modoer_config VALUES ('guestbook_min','10','item');
INSERT INTO modoer_config VALUES ('guestbook_max','50','item');
INSERT INTO modoer_config VALUES ('content_min','10','comment');
INSERT INTO modoer_config VALUES ('content_max','200','comment');
INSERT INTO modoer_config VALUES ('meta_keywords','友情链接','link');
INSERT INTO modoer_config VALUES ('meta_description','Modoer点评系统的友情链接模块！','link');
INSERT INTO modoer_config VALUES ('num_logo','5','link');
INSERT INTO modoer_config VALUES ('num_char','20','link');
INSERT INTO modoer_config VALUES ('open_apply','1','link');
INSERT INTO modoer_config VALUES ('apply','1','card');
INSERT INTO modoer_config VALUES ('applyseccode','1','card');
INSERT INTO modoer_config VALUES ('coin','10','card');
INSERT INTO modoer_config VALUES ('applynum','2','card');
INSERT INTO modoer_config VALUES ('applydes','这里填写申请提交时，显示给会员看的申请说明和条例。','card');
INSERT INTO modoer_config VALUES ('subtitle','最优惠的消费折扣卡','card');
INSERT INTO modoer_config VALUES ('meta_keywords','会员卡','card');
INSERT INTO modoer_config VALUES ('meta_description','modoer点评系统会员卡模块','card');
INSERT INTO modoer_config VALUES ('modelids','','card');
INSERT INTO modoer_config VALUES ('check','1','coupon');
INSERT INTO modoer_config VALUES ('post_item_owner','1','coupon');
INSERT INTO modoer_config VALUES ('watermark','1','coupon');
INSERT INTO modoer_config VALUES ('thumb_width','160','coupon');
INSERT INTO modoer_config VALUES ('thumb_height','100','coupon');
INSERT INTO modoer_config VALUES ('seccode','1','coupon');
INSERT INTO modoer_config VALUES ('listnum','10','coupon');
INSERT INTO modoer_config VALUES ('des','这是是优惠券发布的保证说明！','coupon');
INSERT INTO modoer_config VALUES ('subtitle','最优优惠','coupon');
INSERT INTO modoer_config VALUES ('meta_keywords','优惠券模块','coupon');
INSERT INTO modoer_config VALUES ('meta_description','Modoer点评系统之优惠券模块','coupon');
INSERT INTO modoer_config VALUES ('post_comment','1','coupon');
INSERT INTO modoer_config VALUES ('picture_createthumb_mod','0','modoer');
INSERT INTO modoer_config VALUES ('watermark_postion','0','modoer');
INSERT INTO modoer_config VALUES ('thumb_width','200','product');
INSERT INTO modoer_config VALUES ('thumb_height','150','product');
INSERT INTO modoer_config VALUES ('picture_ext','jpg jpeg png gif','modoer');

DROP TABLE IF EXISTS modoer_coupon_category;
CREATE TABLE modoer_coupon_category (
  catid smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(40) NOT NULL DEFAULT '',
  num mediumint(9) NOT NULL DEFAULT '0',
  listorder smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (catid)
) TYPE=MyISAM;

INSERT INTO modoer_coupon_category VALUES ('1','美食','0','0');
INSERT INTO modoer_coupon_category VALUES ('2','购物','0','0');
INSERT INTO modoer_coupon_category VALUES ('3','休闲','0','0');
INSERT INTO modoer_coupon_category VALUES ('4','女性','0','0');
INSERT INTO modoer_coupon_category VALUES ('5','摄影','0','0');

DROP TABLE IF EXISTS modoer_coupon_print;
CREATE TABLE modoer_coupon_print (
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  couponid mediumint(8) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(20) NOT NULL DEFAULT '',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  point mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY couponid (couponid),
  KEY uid (uid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_coupons;
CREATE TABLE modoer_coupons (
  couponid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  catid smallint(5) unsigned NOT NULL DEFAULT '0',
  sid mediumint(8) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) NOT NULL DEFAULT '0',
  username varchar(30) NOT NULL DEFAULT '',
  thumb varchar(255) NOT NULL DEFAULT '',
  picture varchar(255) NOT NULL DEFAULT '',
  starttime int(10) NOT NULL DEFAULT '0',
  endtime int(10) NOT NULL DEFAULT '0',
  subject varchar(100) NOT NULL DEFAULT '',
  des varchar(50) NOT NULL DEFAULT '',
  content text NOT NULL,
  effect1 mediumint(8) unsigned NOT NULL DEFAULT '0',
  prints mediumint(8) unsigned NOT NULL DEFAULT '0',
  comments mediumint(8) unsigned NOT NULL DEFAULT '0',
  grade smallint(5) unsigned NOT NULL DEFAULT '0',
  flag tinyint(1) unsigned NOT NULL DEFAULT '1',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  pageview int(10) NOT NULL DEFAULT '0',
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  closed_comment tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (couponid),
  KEY sid (sid),
  KEY uid (uid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_datacall;
CREATE TABLE modoer_datacall (
  callid smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  module varchar(60) NOT NULL DEFAULT '',
  calltype varchar(60) NOT NULL DEFAULT '',
  name varchar(50) NOT NULL DEFAULT '',
  fun varchar(60) NOT NULL DEFAULT '',
  var varchar(60) NOT NULL DEFAULT '',
  cachetime mediumint(8) unsigned NOT NULL DEFAULT '0',
  expression text NOT NULL,
  tplname varchar(200) NOT NULL DEFAULT '',
  empty_tplname varchar(200) NOT NULL DEFAULT '',
  closed tinyint(1) unsigned NOT NULL DEFAULT '0',
  hash varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (callid)
) TYPE=MyISAM;

INSERT INTO modoer_datacall VALUES ('1','member','sql','会员_财富榜','sql','mydata','1000','a:6:{s:4:\"from\";s:14:\"{dbpre}members\";s:6:\"select\";s:23:\"uid,username,point,coin\";s:5:\"where\";s:6:\"coin>0\";s:5:\"other\";s:0:\"\";s:7:\"orderby\";s:9:\"coin DESC\";s:5:\"limit\";s:4:\"0,10\";}','member_coin_top','empty_div','0','');
INSERT INTO modoer_datacall VALUES ('2','item','sql','主题_会员参与','sql','mydata','1000','a:6:{s:4:\"from\";s:72:\"{dbpre}membereffect_total mt LEFT JOIN {dbpre}subject s ON (mt.id=s.sid)\";s:6:\"select\";s:52:\"mt.{effect} as effect,s.sid,s.catid,s.name,s.subname\";s:5:\"where\";s:38:\"mt.idtype=\'{idtype}\' AND mt.{effect}>0\";s:5:\"other\";s:0:\"\";s:7:\"orderby\";s:16:\"mt.{effect} DESC\";s:5:\"limit\";s:4:\"0,10\";}','item_subject_effect_li','empty_li','0','');
INSERT INTO modoer_datacall VALUES ('3','item','sql','主题_相关主题','sql','mydata','1000','a:6:{s:4:\"from\";s:14:\"{dbpre}subject\";s:6:\"select\";s:52:\"sid,pid,catid,name,subname,avgsort,pageviews,reviews\";s:5:\"where\";s:41:\"name=\'{name}\' and status=1 and sid!={sid}\";s:5:\"other\";s:0:\"\";s:7:\"orderby\";s:12:\"addtime DESC\";s:5:\"limit\";s:4:\"0,10\";}','item_subject_li','empty_li','0','');
INSERT INTO modoer_datacall VALUES ('4','item','sql','主题_同类主题','sql','mydata','1000','a:6:{s:4:\"from\";s:14:\"{dbpre}subject\";s:6:\"select\";s:52:\"sid,pid,catid,name,subname,avgsort,pageviews,reviews\";s:5:\"where\";s:41:\"catid={catid} and status=1 and sid!={sid}\";s:5:\"other\";s:0:\"\";s:7:\"orderby\";s:12:\"addtime DESC\";s:5:\"limit\";s:4:\"0,10\";}','item_subject_li','empty_li','0','');
INSERT INTO modoer_datacall VALUES ('5','item','sql','主题_附近主题','sql','mydata','1000','a:6:{s:4:\"from\";s:14:\"{dbpre}subject\";s:6:\"select\";s:52:\"sid,pid,catid,name,subname,avgsort,pageviews,reviews\";s:5:\"where\";s:37:\"aid={aid} and status=1 and sid!={sid}\";s:5:\"other\";s:0:\"\";s:7:\"orderby\";s:12:\"addtime DESC\";s:5:\"limit\";s:4:\"0,10\";}','item_subject_li','empty_li','0','');
INSERT INTO modoer_datacall VALUES ('6','item','sql','首页_最新图片','sql','mydata','1000','a:6:{s:4:\"from\";s:15:\"{dbpre}pictures\";s:6:\"select\";s:21:\"picid,title,thumb,sid\";s:5:\"where\";s:8:\"status=1\";s:5:\"other\";s:0:\"\";s:7:\"orderby\";s:12:\"addtime DESC\";s:5:\"limit\";s:3:\"0,7\";}','index_newpic','empty_div','0','');
INSERT INTO modoer_datacall VALUES ('7','item','sql','首页_推荐主题','sql','mydata','1000','a:6:{s:4:\"from\";s:14:\"{dbpre}subject\";s:6:\"select\";s:46:\"sid,aid,name,subname,avgsort,thumb,description\";s:5:\"where\";s:34:\"pid={pid} AND status=1 AND finer>0\";s:5:\"other\";s:0:\"\";s:7:\"orderby\";s:10:\"finer DESC\";s:5:\"limit\";s:3:\"0,8\";}','index_subject_finer','empty_div','0','');
INSERT INTO modoer_datacall VALUES ('8','item','sql','首页_最新点评','sql','mydata','1000','a:6:{s:4:\"from\";s:58:\"{dbpre}review r LEFT JOIN {dbpre}subject s ON(r.sid=s.sid)\";s:6:\"select\";s:204:\"rid,r.sid,r.uid,r.username,r.status,r.sort1,r.sort2,r.sort3,r.sort4,r.sort5,r.sort6,r.sort7,r.sort8,r.price,r.enjoy,r.posttime,r.isupdate,r.flowers,r.responds,r.ip,r.title,r.content,s.name,s.subname,s.pid\";s:5:\"where\";s:10:\"r.status=1\";s:5:\"other\";s:0:\"\";s:7:\"orderby\";s:15:\"r.posttime DESC\";s:5:\"limit\";s:3:\"0,5\";}','index_review','empty_div','0','');
INSERT INTO modoer_datacall VALUES ('9','member','sql','会员_点评专家','sql','mydata','1000','a:6:{s:4:\"from\";s:14:\"{dbpre}members\";s:6:\"select\";s:20:\"uid,username,reviews\";s:5:\"where\";s:9:\"reviews>0\";s:5:\"other\";s:0:\"\";s:7:\"orderby\";s:12:\"reviews DESC\";s:5:\"limit\";s:4:\"0,10\";}','member_review_top','empty_div','0','');
INSERT INTO modoer_datacall VALUES ('10','member','sql','会员_活跃会员','sql','mydata','1000','a:7:{s:4:\"from\";s:15:\"{dbpre}activity\";s:6:\"select\";s:21:\"uid,username, reviews\";s:5:\"where\";s:70:\"dateline>=EXTRACT(YEAR_MONTH FROM (DATE_SUB(NOW(), INTERVAL 1 MONTH)))\";s:5:\"other\";s:0:\"\";s:7:\"orderby\";s:12:\"reviews DESC\";s:5:\"limit\";s:3:\"0,9\";s:9:\"cachetime\";s:4:\"1000\";}','member_face_list','empty_div','0','');
INSERT INTO modoer_datacall VALUES ('11','product','sql','产品_主题产品','sql','mydata','1000','a:7:{s:4:\"from\";s:14:\"{dbpre}product\";s:6:\"select\";s:59:\"pid,catid,subject,grade,description,thumb,comments,pageview\";s:5:\"where\";s:22:\"sid={sid} AND status=1\";s:5:\"other\";s:0:\"\";s:7:\"orderby\";s:24:\"grade DESC,comments DESC\";s:5:\"limit\";s:4:\"0,10\";s:9:\"cachetime\";s:4:\"1000\";}','product_pic_li','empty_li','0','');

DROP TABLE IF EXISTS modoer_exchange_gifts;
CREATE TABLE modoer_exchange_gifts (
  giftid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(200) NOT NULL DEFAULT '',
  sort tinyint(1) unsigned NOT NULL DEFAULT '1',
  available tinyint(1) NOT NULL DEFAULT '0',
  displayorder tinyint(3) NOT NULL DEFAULT '0',
  description text NOT NULL,
  price int(10) unsigned NOT NULL DEFAULT '0',
  num int(10) unsigned NOT NULL DEFAULT '0',
  pageview mediumint(8) unsigned NOT NULL DEFAULT '0',
  thumb varchar(255) NOT NULL DEFAULT '',
  picture varchar(255) NOT NULL DEFAULT '',
  salevolume int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (giftid),
  KEY available (available)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_exchange_log;
CREATE TABLE modoer_exchange_log (
  exchangeid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(25) NOT NULL DEFAULT '',
  giftid mediumint(8) unsigned NOT NULL DEFAULT '0',
  giftname varchar(200) NOT NULL DEFAULT '',
  price int(10) unsigned NOT NULL DEFAULT '0',
  number int(10) unsigned NOT NULL DEFAULT '1',
  status tinyint(1) NOT NULL DEFAULT '1',
  status_extra varchar(255) NOT NULL DEFAULT '',
  exchangetime int(10) NOT NULL DEFAULT '0',
  contact mediumtext NOT NULL,
  checker varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (exchangeid),
  KEY uid (uid),
  KEY giftid (giftid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_exchange_serial;
CREATE TABLE modoer_exchange_serial (
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  giftid mediumint(8) unsigned NOT NULL DEFAULT '0',
  serial varchar(255) NOT NULL DEFAULT '',
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  sendtime int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY giftid (giftid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_favorites;
CREATE TABLE modoer_favorites (
  fid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(20) NOT NULL DEFAULT '',
  sid mediumint(8) unsigned NOT NULL DEFAULT '0',
  addtime int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (fid),
  KEY shopid (sid),
  KEY addtime (addtime),
  KEY uid (uid,addtime)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_field;
CREATE TABLE modoer_field (
  fieldid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  idtype varchar(30) NOT NULL DEFAULT '',
  id smallint(5) NOT NULL DEFAULT '0',
  tablename varchar(25) NOT NULL DEFAULT '',
  fieldname varchar(50) NOT NULL DEFAULT '',
  title varchar(100) NOT NULL DEFAULT '',
  unit varchar(100) NOT NULL DEFAULT '',
  style varchar(255) NOT NULL DEFAULT '',
  template text NOT NULL,
  note mediumtext NOT NULL,
  type varchar(20) NOT NULL DEFAULT '',
  listorder smallint(5) NOT NULL DEFAULT '0',
  allownull tinyint(1) unsigned NOT NULL DEFAULT '1',
  enablesearch tinyint(1) unsigned NOT NULL DEFAULT '0',
  iscore tinyint(1) NOT NULL DEFAULT '0',
  isadminfield varchar(1) NOT NULL DEFAULT '0',
  show_list tinyint(1) unsigned NOT NULL DEFAULT '0',
  show_detail tinyint(1) unsigned NOT NULL DEFAULT '1',
  regular varchar(255) NOT NULL DEFAULT '',
  errmsg varchar(255) NOT NULL DEFAULT '',
  datatype varchar(60) NOT NULL DEFAULT '',
  config text NOT NULL,
  disable tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (fieldid),
  KEY tablename (tablename)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_flowers;
CREATE TABLE modoer_flowers (
  fid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  rid mediumint(8) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(16) NOT NULL DEFAULT '',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (fid),
  KEY reviewid (rid),
  KEY uid (uid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_friends;
CREATE TABLE modoer_friends (
  fid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  friend_uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  addtime int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (fid),
  KEY addtime (addtime,uid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_gbooks;
CREATE TABLE modoer_gbooks (
  gid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  gbuid mediumint(8) unsigned NOT NULL DEFAULT '0',
  gbusername varchar(16) NOT NULL DEFAULT '',
  content text NOT NULL,
  posttime int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (gid),
  KEY uid (uid,posttime)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_getpassword;
CREATE TABLE modoer_getpassword (
  getpwid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  secode varchar(8) NOT NULL DEFAULT '',
  posttime int(10) NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (getpwid),
  KEY secode (secode,status)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_guestbook;
CREATE TABLE modoer_guestbook (
  guestbookid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  sid mediumint(8) NOT NULL DEFAULT '0',
  username varchar(20) NOT NULL DEFAULT '',
  uid mediumint(9) unsigned NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  content text NOT NULL,
  ip varchar(15) NOT NULL DEFAULT '',
  status tinyint(1) unsigned NOT NULL DEFAULT '0',
  reply text NOT NULL,
  replytime int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (guestbookid),
  KEY id (sid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_hook;
CREATE TABLE modoer_hook (
  hookid smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  hook_module varchar(30) NOT NULL DEFAULT '',
  hook_position varchar(60) NOT NULL DEFAULT '',
  module varchar(30) NOT NULL DEFAULT '',
  filename varchar(255) NOT NULL DEFAULT '',
  disable tinyint(1) unsigned NOT NULL DEFAULT '0',
  des varchar(255) NOT NULL DEFAULT '',
  listorder smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (hookid)
) TYPE=MyISAM;


DROP TABLE IF EXISTS modoer_membereffect;
CREATE TABLE modoer_membereffect (
  id mediumint(8) unsigned NOT NULL DEFAULT '0',
  idtype varchar(30) NOT NULL DEFAULT '',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(20) NOT NULL DEFAULT '',
  dateline int(10) NOT NULL DEFAULT '0',
  effect1 tinyint(1) unsigned NOT NULL DEFAULT '0',
  effect2 tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY id (id,idtype)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_membereffect_total;
CREATE TABLE modoer_membereffect_total (
  id mediumint(8) unsigned NOT NULL DEFAULT '0',
  idtype varchar(30) NOT NULL DEFAULT '',
  effect1 int(10) unsigned NOT NULL DEFAULT '0',
  effect2 int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_members;
CREATE TABLE modoer_members (
  uid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(60) NOT NULL DEFAULT '',
  password varchar(32) NOT NULL DEFAULT '',
  username varchar(16) NOT NULL DEFAULT '',
  point int(10) NOT NULL DEFAULT '0',
  rmb decimal(9,2) unsigned NOT NULL default '0.00',
  coin int(10) NOT NULL DEFAULT '0',
  newmsg smallint(5) unsigned NOT NULL DEFAULT '0',
  regdate int(10) unsigned NOT NULL DEFAULT '0',
  logintime int(10) unsigned NOT NULL DEFAULT '0',
  loginip varchar(16) NOT NULL DEFAULT '',
  logincount mediumint(8) unsigned NOT NULL DEFAULT '0',
  groupid smallint(2) NOT NULL DEFAULT '1',
  nextgroupid smallint(5) unsigned NOT NULL DEFAULT '0',
  nexttime int(10) unsigned NOT NULL DEFAULT '0',
  subjects int(10) unsigned NOT NULL DEFAULT '0',
  reviews int(10) unsigned NOT NULL DEFAULT '0',
  responds int(10) unsigned NOT NULL DEFAULT '0',
  flowers int(10) unsigned NOT NULL DEFAULT '0',
  pictures int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (uid),
  UNIQUE KEY username (username),
  KEY email (email),
  KEY groupid (groupid),
  KEY point (point),
  KEY coin (coin)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_menus;
CREATE TABLE modoer_menus (
  menuid smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  parentid smallint(5) unsigned NOT NULL DEFAULT '0',
  isclosed tinyint(1) NOT NULL DEFAULT '0',
  isfolder tinyint(1) unsigned NOT NULL DEFAULT '0',
  title varchar(100) NOT NULL DEFAULT '',
  scriptnav varchar(60) NOT NULL DEFAULT '',
  url varchar(255) NOT NULL DEFAULT '',
  icon varchar(60) NOT NULL DEFAULT '',
  target varchar(15) NOT NULL DEFAULT '',
  listorder smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (menuid)
) TYPE=MyISAM;


INSERT INTO modoer_menus VALUES ('1','0','0','1','头部菜单','','','','','1');
INSERT INTO modoer_menus VALUES ('49','1','0','0','首页','index','modoer/index','','_self','1');
INSERT INTO modoer_menus VALUES ('3','0','0','1','后台快捷菜单','','','','','5');
INSERT INTO modoer_menus VALUES ('53','3','0','0','调用管理','','?module=modoer&act=datacall&op=list','','main','3');
INSERT INTO modoer_menus VALUES ('54','3','0','0','更新网站缓存','','?module=modoer&act=tools&op=cache','','main','4');
INSERT INTO modoer_menus VALUES ('62','1','0','0','主题','item','item/category','','','4');
INSERT INTO modoer_menus VALUES ('80','0','0','1','个人空间菜单','','','','','4');
INSERT INTO modoer_menus VALUES ('75','3','0','0','菜单管理','','?module=modoer&act=menu','','','5');
INSERT INTO modoer_menus VALUES ('66','0','0','1','底部菜单','','','','','2');
INSERT INTO modoer_menus VALUES ('68','66','0','0','联系我们','','#','','','0');
INSERT INTO modoer_menus VALUES ('69','66','0','0','广告服务','','#','','','0');
INSERT INTO modoer_menus VALUES ('70','66','0','0','服务条款','','#','','','0');
INSERT INTO modoer_menus VALUES ('71','66','0','0','网站地图','','#','','','0');
INSERT INTO modoer_menus VALUES ('72','66','0','0','使用帮助','','#','','','0');
INSERT INTO modoer_menus VALUES ('73','66','0','0','诚聘英才','','#','','','0');
INSERT INTO modoer_menus VALUES ('76','3','0','0','主题审核','','?module=item&act=subject_check','','','1');
INSERT INTO modoer_menus VALUES ('77','3','0','0','点评审核','','?module=review&act=review&op=checklist','','','2');
INSERT INTO modoer_menus VALUES ('81','80','0','0','首页','space_index','space/index/uid/(uid)','','','1');
INSERT INTO modoer_menus VALUES ('82','80','0','0','我发表的点评','space_reviews','space/reviews/uid/(uid)','','','2');
INSERT INTO modoer_menus VALUES ('83','80','0','0','我添加的主题','space_subjects','space/subjects/uid/(uid)','','','3');
INSERT INTO modoer_menus VALUES ('84','80','0','0','我的好友','space_friends','space/friends/uid/(uid)','','','4');
INSERT INTO modoer_menus VALUES ('88','1','0','0','礼品','exchange','exchange/index','','','9');
INSERT INTO modoer_menus VALUES ('90','1','0','0','资讯','article','article/index','','','3');
INSERT INTO modoer_menus VALUES ('93','1','0','0','会员卡','card','card/index','','','11');
INSERT INTO modoer_menus VALUES ('94','1','0','0','优惠券','coupon','coupon/index','','','10');
INSERT INTO modoer_menus VALUES ('95','1','0','0','产品库','product','product/index','','','12');
INSERT INTO modoer_menus VALUES ('96','1','0','0','点评','review','review/index','','','5');

DROP TABLE IF EXISTS modoer_model;
CREATE TABLE modoer_model (
  modelid smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(30) NOT NULL DEFAULT '',
  tablename varchar(20) NOT NULL DEFAULT '',
  description varchar(255) NOT NULL DEFAULT '',
  usearea tinyint(1) NOT NULL DEFAULT '0',
  item_name varchar(200) NOT NULL DEFAULT '',
  item_unit varchar(200) NOT NULL DEFAULT '',
  tplname_list varchar(200) NOT NULL DEFAULT '',
  tplname_detail varchar(200) NOT NULL DEFAULT '',
  disable tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (modelid)
) TYPE=MyISAM;

INSERT INTO modoer_model VALUES ('1','商铺模型','subject_shops','','1','商铺','户','item_subject_list','item_subject_detail','0');

DROP TABLE IF EXISTS modoer_modules;
CREATE TABLE modoer_modules (
  moduleid smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  flag varchar(30) NOT NULL DEFAULT '',
  iscore tinyint(1) NOT NULL DEFAULT '0',
  listorder smallint(5) unsigned NOT NULL DEFAULT '0',
  name varchar(60) NOT NULL DEFAULT '',
  directory varchar(100) NOT NULL DEFAULT '',
  disable tinyint(1) unsigned NOT NULL DEFAULT '0',
  config text NOT NULL,
  version varchar(60) NOT NULL DEFAULT '',
  releasetime date NOT NULL DEFAULT '0000-00-00',
  reliant varchar(255) NOT NULL DEFAULT '',
  author varchar(255) NOT NULL DEFAULT '',
  introduce text NOT NULL,
  siteurl varchar(255) NOT NULL DEFAULT '',
  email varchar(255) NOT NULL DEFAULT '',
  copyright varchar(255) NOT NULL DEFAULT '',
  checkurl varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (moduleid)
) TYPE=MyISAM;

INSERT INTO modoer_modules VALUES ('1','member','1','8','会员','member','0','','1.1','2008-09-30','','Moufer Studio',' ','http://www.modoer.com','moufer@163.com','Moufer Studio','');
INSERT INTO modoer_modules VALUES ('2','item','1','1','主题','item','0','','2.5','2011-05-24','','Moufer Studio',' ','http://www.modoer.com','moufer@163.com','Moufer Studio','');
INSERT INTO modoer_modules VALUES ('3','space','1','9','个人空间','space','0','','1.1','2008-09-30','','Moufer Studio','','http://www.modoer.com','moufer@163.com','Moufer Studio','');
INSERT INTO modoer_modules VALUES ('4','link','0','10','友情链接','link','0','','2.0','2010-05-04','','moufer','友情链接模块','http://www.modoer.com','moufer@163.com','Moufer Studio','http://www.modoer.com/info/module/comment.php');
INSERT INTO modoer_modules VALUES ('5','product','0','2','主题产品','product','0','','1.1','2010-03-27','','moufer','用于商铺类主题的产品列表','http://www.modoer.com','moufer@163.com','Moufer Studio','http://www.modoer.com/info/module/product.php');
INSERT INTO modoer_modules VALUES ('6','comment','0','6','会员评论','comment','0','','1.0','2010-04-01','','moufer','评论模块可用于其他需要进行评论的模块','http://www.modoer.com','moufer@163.com','Moufer Studio','http://www.modoer.com/info/module/comment.php');
INSERT INTO modoer_modules VALUES ('7','exchange','0','5','礼品兑换','exchange','0','','2.0','2010-04-12','','moufer','使用网站金币兑换礼品，消费金币','http://www.modoer.com','moufer@163.com','Moufer Studio','http://www.modoer.com/info/module/exchange.php');
INSERT INTO modoer_modules VALUES ('8','article','0','3','新闻资讯','article','0','','2.0','2010-04-14','','moufer','文章信息，发布网站信息和主题资讯','http://www.modoer.com','moufer@163.com','Moufer Studio','http://www.modoer.com/info/module/article.php');
INSERT INTO modoer_modules VALUES ('9','card','0','7','会员卡','card','0','','2.0','2010-05-06','item','moufer','会员卡模块用户管理消费类主题提供优惠折扣信息','http://www.modoer.com','moufer@163.com','Moufer Studio','http://www.modoer.com/info/module/card.php');
INSERT INTO modoer_modules VALUES ('10','coupon','0','4','优惠券','coupon','0','','2.0','2010-05-10','','moufer','优惠券模块，提供分享和打印折扣和优惠信息','http://www.modoer.com','moufer@163.com','Moufer Studio','http://www.modoer.com/info/module/coupon.php');
INSERT INTO modoer_modules VALUES ('11','adv','0','10','广告','adv','0','','2.0','2010-12-13','','moufer','自定义广告模块','http://www.modoer.com','moufer@163.com','Moufer Studio','http://www.modoer.com/info/module/adv.php');
INSERT INTO modoer_modules VALUES ('12','review','1','2','点评','review','0','','2.5','2011-05-24','','Moufer Studio','点评模块','http://www.modoer.com','moufer@163.com','Moufer Studio','');

DROP TABLE IF EXISTS modoer_mylinks;
CREATE TABLE modoer_mylinks (
  linkid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(40) NOT NULL DEFAULT '',
  link varchar(100) NOT NULL DEFAULT '',
  logo varchar(100) NOT NULL DEFAULT '',
  des varchar(255) NOT NULL DEFAULT '',
  displayorder int(8) NOT NULL DEFAULT '0',
  ischeck tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (linkid)
) TYPE=MyISAM;

INSERT INTO modoer_mylinks VALUES ('1','Modoer点评系统','http://www.modoer.com','','Modoer 是一款点评网站管理系统，采用 PHP+MYSQL 设计，开放全部源码','1','1');

DROP TABLE IF EXISTS modoer_mysubject;
CREATE TABLE modoer_mysubject (
  id mediumint(8) unsigned NOT NULL auto_increment,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  sid mediumint(8) unsigned NOT NULL DEFAULT '0',
  modelid smallint(5) NOT NULL DEFAULT '0',
  lasttime int(10) unsigned NOT NULL DEFAULT '0',
  expirydate int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (id)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_pictures;
CREATE TABLE modoer_pictures (
  picid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  sid mediumint(8) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(16) NOT NULL DEFAULT '',
  title varchar(60) NOT NULL DEFAULT '',
  thumb varchar(255) NOT NULL DEFAULT '',
  filename varchar(255) NOT NULL DEFAULT '',
  width smallint(5) unsigned NOT NULL DEFAULT '0',
  height smallint(5) unsigned NOT NULL DEFAULT '0',
  size int(10) unsigned NOT NULL DEFAULT '0',
  comments varchar(60) NOT NULL DEFAULT '',
  sort tinyint(1) NOT NULL DEFAULT '0',
  browse int(10) NOT NULL DEFAULT '0',
  addtime int(10) NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (picid),
  KEY uid (uid,sid),
  KEY sid (sid,status)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_pmsgs;
CREATE TABLE modoer_pmsgs (
  pmid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  senduid mediumint(8) unsigned NOT NULL DEFAULT '0',
  recvuid mediumint(8) unsigned NOT NULL DEFAULT '0',
  content mediumtext NOT NULL,
  subject varchar(60) NOT NULL DEFAULT '',
  posttime int(10) NOT NULL DEFAULT '0',
  new tinyint(1) NOT NULL DEFAULT '1',
  delflag tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (pmid),
  KEY senduid (senduid,posttime),
  KEY recvuid (recvuid,posttime),
  KEY new (new,recvuid,posttime)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_product;
CREATE TABLE modoer_product (
  pid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  modelid smallint(5) unsigned NOT NULL DEFAULT '0',
  sid mediumint(8) unsigned NOT NULL DEFAULT '0',
  catid mediumint(8) unsigned NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(20) NOT NULL DEFAULT '',
  subject varchar(60) NOT NULL DEFAULT '',
  price decimal(9,2) unsigned NOT NULL default '0',
  grade smallint(5) NOT NULL DEFAULT '0',
  pageview mediumint(8) unsigned NOT NULL DEFAULT '0',
  comments mediumint(8) NOT NULL DEFAULT '0',
  picture varchar(255) NOT NULL DEFAULT '',
  thumb varchar(255) NOT NULL DEFAULT '',
  description varchar(255) NOT NULL DEFAULT '',
  closed_comment tinyint(1) NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1',
  listorder smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (pid),
  KEY catid (sid,catid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_product_category;
CREATE TABLE modoer_product_category (
  catid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  sid mediumint(8) unsigned NOT NULL DEFAULT '0',
  name varchar(20) NOT NULL DEFAULT '',
  listorder smallint(5) NOT NULL DEFAULT '0',
  num mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (catid),
  KEY sid (sid,catid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_product_model;
CREATE TABLE modoer_product_model (
  modelid smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(30) NOT NULL DEFAULT '',
  tablename varchar(20) NOT NULL DEFAULT '',
  description varchar(255) NOT NULL DEFAULT '',
  usearea tinyint(1) NOT NULL DEFAULT '0',
  item_name varchar(200) NOT NULL DEFAULT '',
  item_unit varchar(200) NOT NULL DEFAULT '',
  tplname_list varchar(200) NOT NULL DEFAULT '',
  tplname_detail varchar(200) NOT NULL DEFAULT '',
  disable tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (modelid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_reports;
CREATE TABLE modoer_reports (
  reportid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  rid mediumint(8) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(16) NOT NULL DEFAULT '',
  email varchar(60) NOT NULL DEFAULT '',
  sort tinyint(1) NOT NULL DEFAULT '0',
  reportcontent mediumtext NOT NULL,
  disposal tinyint(1) NOT NULL DEFAULT '0',
  posttime int(10) NOT NULL DEFAULT '0',
  disposaltime int(10) NOT NULL DEFAULT '0',
  reportremark mediumtext NOT NULL,
  update_point tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (reportid),
  KEY disposal (disposal),
  KEY rid (rid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_responds;
CREATE TABLE modoer_responds (
  respondid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  rid mediumint(8) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(20) NOT NULL DEFAULT '',
  content text NOT NULL,
  posttime int(10) NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1',
  ip varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (respondid),
  KEY uid (uid,status),
  KEY reviewid (rid,status)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_review;
CREATE TABLE modoer_review (
  rid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  idtype varchar(30) NOT NULL default '',
  id mediumint(8) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(20) NOT NULL DEFAULT '',
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  pcatid smallint(5) unsigned NOT NULL DEFAULT '0',
  sort1 tinyint(1) unsigned NOT NULL DEFAULT '0',
  sort2 tinyint(1) unsigned NOT NULL DEFAULT '0',
  sort3 tinyint(1) unsigned NOT NULL DEFAULT '0',
  sort4 tinyint(1) unsigned NOT NULL DEFAULT '0',
  sort5 tinyint(1) unsigned NOT NULL DEFAULT '0',
  sort6 tinyint(1) unsigned NOT NULL DEFAULT '0',
  sort7 tinyint(1) unsigned NOT NULL DEFAULT '0',
  sort8 tinyint(1) unsigned NOT NULL DEFAULT '0',
  price int(10) unsigned NOT NULL DEFAULT '0',
  best tinyint(1) unsigned NOT NULL DEFAULT '0',
  digest tinyint(1) NOT NULL default '0',
  havepic tinyint(1) NOT NULL default '0',
  posttime int(10) NOT NULL DEFAULT '0',
  isupdate tinyint(1) NOT NULL DEFAULT '0',
  flowers int(8) unsigned NOT NULL DEFAULT '0',
  responds int(8) unsigned NOT NULL DEFAULT '0',
  ip varchar(15) NOT NULL DEFAULT '',
  subject varchar(255) NOT NULL default '',
  title varchar(60) NOT NULL DEFAULT '',
  content text NOT NULL,
  taggroup text NOT NULL,
  pictures text NOT NULL,
  PRIMARY KEY (rid),
  KEY sid (id,status),
  KEY uid (uid,status)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_review_opt;
CREATE TABLE modoer_review_opt (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  gid SMALLINT(5) UNSIGNED NOT NULL DEFAULT  '0',
  flag varchar(10) NOT NULL DEFAULT '',
  name varchar(50) NOT NULL DEFAULT '',
  listorder smallint(5) NOT NULL DEFAULT '0',
  enable tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
) TYPE=MyISAM;

INSERT INTO modoer_review_opt VALUES ('1','1','sort1','口味','1','1');
INSERT INTO modoer_review_opt VALUES ('2','1','sort2','服务','2','1');
INSERT INTO modoer_review_opt VALUES ('3','1','sort3','环境','3','1');
INSERT INTO modoer_review_opt VALUES ('4','1','sort4','性价比','4','1');
INSERT INTO modoer_review_opt VALUES ('5','1','sort5','R5','5','0');
INSERT INTO modoer_review_opt VALUES ('6','1','sort6','R6','6','0');
INSERT INTO modoer_review_opt VALUES ('7','1','sort7','R7','7','0');
INSERT INTO modoer_review_opt VALUES ('8','1','sort8','R8','8','0');

DROP TABLE IF EXISTS modoer_review_opt_group;
CREATE TABLE modoer_review_opt_group (
	gid smallint(5) unsigned NOT NULL auto_increment,
	name varchar(60) NOT NULL default '',
	des varchar(255) NOT NULL default '',
	PRIMARY KEY (gid)
) TYPE=MyISAM;

INSERT INTO modoer_review_opt_group VALUES ('1','默认点评项组','系统默认安装');

DROP TABLE IF EXISTS modoer_search_cache;
CREATE TABLE modoer_search_cache (
  ssid mediumint(8) NOT NULL AUTO_INCREMENT,
  keyword varchar(60) NOT NULL DEFAULT '0',
  count mediumint(8) NOT NULL DEFAULT '0',
  total mediumint(8) NOT NULL DEFAULT '0',
  catid smallint(5) NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (ssid),
  KEY catid (catid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_spaces;
CREATE TABLE modoer_spaces (
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  space_styleid smallint(3) NOT NULL DEFAULT '0',
  spacename varchar(30) NOT NULL DEFAULT '',
  spacedescribe varchar(50) NOT NULL DEFAULT '',
  pageview int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (uid),
  KEY pageviews (pageview)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_subject;
CREATE TABLE modoer_subject (
  sid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  domain char(50) NOT NULL DEFAULT '',
  aid smallint(5) unsigned NOT NULL DEFAULT '0',
  pid smallint(5) unsigned NOT NULL DEFAULT '0',
  catid smallint(5) unsigned NOT NULL DEFAULT '0',
  name varchar(60) NOT NULL DEFAULT '',
  subname varchar(60) NOT NULL DEFAULT '',
  avgsort decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  sort1 decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  sort2 decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  sort3 decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  sort4 decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  sort5 decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  sort6 decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  sort7 decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  sort8 decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  avgprice int(10) unsigned NOT NULL DEFAULT '0',
  best int(10) unsigned NOT NULL DEFAULT '0',
  reviews int(10) unsigned NOT NULL DEFAULT '0',
  guestbooks int(10) unsigned NOT NULL DEFAULT '0',
  pictures int(10) unsigned NOT NULL DEFAULT '0',
  pageviews int(10) unsigned NOT NULL DEFAULT '1',
  products mediumint(8) unsigned NOT NULL DEFAULT '0',
  coupons mediumint(8) unsigned NOT NULL DEFAULT '0',
  favorites mediumint(8) unsigned NOT NULL default '0',
  finer tinyint(3) unsigned NOT NULL DEFAULT '0',
  level tinyint(3) unsigned NOT NULL DEFAULT '0',
  owner varchar(20) NOT NULL DEFAULT '',
  cuid mediumint(8) unsigned NOT NULL DEFAULT '0',
  creator varchar(20) NOT NULL DEFAULT '',
  addtime int(10) unsigned NOT NULL DEFAULT '0',
  video varchar(255) NOT NULL DEFAULT '',
  thumb varchar(255) NOT NULL DEFAULT '',
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  map_lng decimal(8,5) NOT NULL DEFAULT '0.00000',
  map_lat decimal(8,5) NOT NULL DEFAULT '0.00000',
  description varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (sid),
  KEY aid (aid,catid),
  KEY pid (pid),
  KEY name (name),
  KEY doamin (domain)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_subject_shops;
CREATE TABLE modoer_subject_shops (
  sid mediumint(8) unsigned NOT NULL DEFAULT '0',
  content mediumtext NOT NULL,
  templateid smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (sid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_subjectatt;
CREATE TABLE modoer_subjectatt (
	id mediumint(8) unsigned NOT NULL auto_increment,
	sid mediumint(8) unsigned NOT NULL default '0',
	attid mediumint(8) unsigned NOT NULL default '0',
	att_catid mediumint(8) unsigned NOT NULL default '0',
	PRIMARY KEY  (id),
	KEY sid (sid,attid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_subjectapply;
CREATE TABLE modoer_subjectapply (
  applyid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  sid mediumint(8) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(20) NOT NULL DEFAULT '',
  status tinyint(1) unsigned NOT NULL DEFAULT '0',
  applyname varchar(100) NOT NULL DEFAULT '',
  contact varchar(255) NOT NULL DEFAULT '',
  pic varchar(255) NOT NULL DEFAULT '',
  content mediumtext NOT NULL,
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  checker varchar(30) NOT NULL DEFAULT '',
  returned text NOT NULL,
  PRIMARY KEY (applyid),
  KEY sid (sid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_subjectfield;
CREATE TABLE modoer_subjectfield (
  fieldid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  modelid smallint(5) NOT NULL  DEFAULT '0',
  tablename varchar(25) NOT NULL DEFAULT '',
  fieldname varchar(50) NOT NULL DEFAULT '',
  title varchar(100) NOT NULL DEFAULT '',
  unit varchar(100) NOT NULL DEFAULT '',
  style varchar(255) NOT NULL DEFAULT '',
  template text NOT NULL,
  note mediumtext NOT NULL,
  type varchar(20) NOT NULL DEFAULT '',
  listorder smallint(5) NOT NULL DEFAULT '0',
  allownull tinyint(1) unsigned NOT NULL DEFAULT '1',
  enablesearch tinyint(1) unsigned NOT NULL DEFAULT '0',
  iscore tinyint(1) NOT NULL DEFAULT '0',
  isadminfield varchar(1) NOT NULL DEFAULT '0',
  show_list tinyint(1) unsigned NOT NULL DEFAULT '0',
  show_detail tinyint(1) unsigned NOT NULL DEFAULT '1',
  regular varchar(255) NOT NULL DEFAULT '',
  errmsg varchar(255) NOT NULL DEFAULT '',
  datatype varchar(60) NOT NULL DEFAULT '',
  config text NOT NULL,
  disable tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (fieldid),
  KEY tablename (tablename)
) TYPE=MyISAM;

INSERT INTO modoer_subjectfield VALUES ('1','1','subject','aid','地区','','','','','area','0','0','1','2','0','0','1','/[0-9]+/','未选择地区','varchar(6)','a:1:{s:7:\"default\";s:1:\"0\";}','0');
INSERT INTO modoer_subjectfield VALUES ('2','1','subject','catid','分类','','','','','category','1','0','1','2','0','0','1','/[0-9]+/','未选择分类','smallint(5)','a:1:{s:7:\"default\";s:1:\"0\";}','0');
INSERT INTO modoer_subjectfield VALUES ('3','1','subject','name','名称','','','','','text','2','0','1','1','0','0','1','','','VARCHAR(255)','a:3:{s:3:\"len\";i:80;s:7:\"default\";s:0:\"\";s:4:\"size\";i:20;}','0');
INSERT INTO modoer_subjectfield VALUES ('4','1','subject','subname','子名称','','','','','text','3','1','1','1','0','0','1','','','VARCHAR(255)','a:3:{s:3:\"len\";i:80;s:7:\"default\";s:0:\"\";s:4:\"size\";i:20;}','0');
INSERT INTO modoer_subjectfield VALUES ('5','1','subject','mappoint','地图坐标','','','','','mappoint','4','0','0','1','0','0','1','/[0-9a-z]+,[0-9a-z]+/i','地图坐标不正确','varchar(60)','a:2:{s:7:\"default\";s:0:\"\";s:4:\"size\";s:2:\"30\";}','0');
INSERT INTO modoer_subjectfield VALUES ('6','1','subject','video','视频地址','','','','','video','5','1','0','0','0','0','1','','','varchar(255)','a:2:{s:7:\"default\";s:0:\"\";s:4:\"size\";s:2:\"30\";}','0');
INSERT INTO modoer_subjectfield VALUES ('8','1','subject','description','简介','','','','','text','7','1','0','1','0','0','1','','','VARCHAR(255)','a:3:{s:3:\"len\";i:255;s:7:\"default\";s:0:\"\";s:4:\"size\";i:60;}','0');
INSERT INTO modoer_subjectfield VALUES ('9','1','subject','status','状态','','','','','status','99','0','1','1','1','0','1','/[0-9]+/','未选择状态','tinyint(1)','a:1:{s:7:\"default\";i:1;}','0');
INSERT INTO modoer_subjectfield VALUES ('10','1','subject','level','等级','','','','','level','92','0','1','1','1','0','1','/[0-9]+/','未选择点评对象等级','tinyint(3)','a:1:{s:7:\"default\";i:0;}','0');
INSERT INTO modoer_subjectfield VALUES ('11','1','subject','finer','推荐度','','','','','numeric','91','1','0','1','1','0','0','','','SMALLINT(5)','a:4:{s:3:\"min\";i:0;s:3:\"max\";i:255;s:5:\"point\";s:1:\"0\";s:7:\"default\";s:1:\"0\";}','0');
INSERT INTO modoer_subjectfield VALUES ('12','1','subject_shops','content','详细介绍','','','','','textarea','90','0','0','0','0','0','1','','','MEDIUMTEXT','a:6:{s:5:\"width\";s:3:\"99%\";s:6:\"height\";s:5:\"200px\";s:4:\"html\";s:1:\"2\";s:7:\"default\";s:0:\"\";s:11:\"charnum_sup\";i:0;s:11:\"charnum_sub\";i:1000;}','0');
INSERT INTO modoer_subjectfield VALUES ('13','1','subject_shops','templateid','主题风格','','','','','template','98','0','0','1','1','0','1','/[0-9]+/','无效的主题风格，请返回设置。','smallint(5)','a:1:{s:7:\"default\";i:0;}','0');
DROP TABLE IF EXISTS modoer_subjectlog;
CREATE TABLE modoer_subjectlog (
  upid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  sid mediumint(8) unsigned NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  username varchar(16) NOT NULL DEFAULT '',
  email varchar(60) NOT NULL DEFAULT '',
  ismappoint tinyint(1) unsigned NOT NULL DEFAULT '0',
  upcontent mediumtext NOT NULL,
  disposal tinyint(1) NOT NULL DEFAULT '0',
  posttime int(10) NOT NULL DEFAULT '0',
  upremark mediumtext NOT NULL,
  disposaltime int(10) NOT NULL DEFAULT '0',
  update_point tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (upid),
  KEY sid (sid),
  KEY disposal (disposal)
) TYPE=MyISAM;


DROP TABLE IF EXISTS modoer_tag_data;
CREATE TABLE modoer_tag_data (
  stid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  tagid int(10) unsigned NOT NULL DEFAULT '0',
  tgid smallint(5) unsigned NOT NULL DEFAULT '0',
  id mediumint(8) unsigned NOT NULL DEFAULT '0',
  tagname varchar(25) NOT NULL DEFAULT '',
  total int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (stid),
  KEY tagid (tagid),
  KEY id (id)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_taggroup;
CREATE TABLE modoer_taggroup (
  tgid smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(30) NOT NULL DEFAULT '',
  des varchar(200) NOT NULL DEFAULT '',
  sort tinyint(1) unsigned NOT NULL DEFAULT '0',
  options text NOT NULL,
  PRIMARY KEY (tgid)
) TYPE=MyISAM;

INSERT INTO modoer_taggroup VALUES ('1','点评标签','商铺标签说明','1','');

DROP TABLE IF EXISTS modoer_tags;
CREATE TABLE modoer_tags (
  tagid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  tagname varchar(20) NOT NULL DEFAULT '',
  closed tinyint(1) NOT NULL DEFAULT '0',
  total int(10) unsigned NOT NULL DEFAULT '0',
  dateline int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (tagid),
  KEY total (total),
  KEY closed (closed),
  KEY tagname (tagname)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_templates;
CREATE TABLE modoer_templates (
  templateid smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(20) NOT NULL DEFAULT '',
  directory varchar(100) NOT NULL DEFAULT '',
  copyright varchar(45) NOT NULL DEFAULT '',
  tpltype varchar(15) NOT NULL DEFAULT '',
  bind tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (templateid)
) TYPE=MyISAM;

INSERT INTO modoer_templates VALUES ('1','默认模板','default','Moufer Studio','main','1');

DROP TABLE IF EXISTS modoer_travelers;
CREATE TABLE modoer_travelers (
  tid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  tuid mediumint(8) unsigned NOT NULL DEFAULT '0',
  tusername varchar(16) NOT NULL DEFAULT '',
  addtime int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (tid),
  KEY uid (uid,addtime),
  KEY tuid (tuid,addtime)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_usergroups;
CREATE TABLE modoer_usergroups (
  groupid smallint(6) NOT NULL AUTO_INCREMENT,
  grouptype enum('member','special','system') DEFAULT 'member',
  groupname char(16) DEFAULT NULL DEFAULT '',
  point int(10) NOT NULL DEFAULT '0',
  color varchar(7) NOT NULL DEFAULT '',
  access text NOT NULL,
  PRIMARY KEY (groupid)
) TYPE=MyISAM;

INSERT INTO modoer_usergroups VALUES ('1','system','游客','0','#808080','a:5:{s:16:\"member_forbidden\";s:1:\"0\";s:13:\"item_subjects\";s:2:\"-1\";s:12:\"item_reviews\";s:2:\"-1\";s:13:\"item_pictures\";s:2:\"-1\";s:15:\"comment_disable\";s:1:\"1\";}');
INSERT INTO modoer_usergroups VALUES ('2','system','禁止访问','0','#808080','a:5:{s:16:\"member_forbidden\";s:1:\"1\";s:13:\"item_subjects\";s:2:\"-1\";s:12:\"item_reviews\";s:2:\"-1\";s:13:\"item_pictures\";s:2:\"-1\";s:15:\"comment_disable\";s:1:\"1\";}');
INSERT INTO modoer_usergroups VALUES ('3','system','禁止发言','0','#808080','a:5:{s:16:\"member_forbidden\";s:1:\"0\";s:13:\"item_subjects\";s:2:\"-1\";s:12:\"item_reviews\";s:2:\"-1\";s:13:\"item_pictures\";s:2:\"-1\";s:15:\"comment_disable\";s:1:\"1\";}');
INSERT INTO modoer_usergroups VALUES ('10','member','注册会员','0','','a:7:{s:16:\"member_forbidden\";s:1:\"0\";s:13:\"item_subjects\";s:0:\"\";s:12:\"item_reviews\";s:0:\"\";s:13:\"item_pictures\";s:2:\"10\";s:15:\"comment_disable\";s:1:\"0\";s:16:\"exchange_disable\";s:1:\"0\";s:12:\"article_post\";s:1:\"0\";}');
INSERT INTO modoer_usergroups VALUES ('12','member','青铜会员','300','','a:2:{s:16:\"member_forbidden\";s:1:\"0\";s:13:\"item_pictures\";s:2:\"20\";}');
INSERT INTO modoer_usergroups VALUES ('13','member','白银会员','1000','','a:2:{s:16:\"member_forbidden\";s:1:\"0\";s:13:\"item_pictures\";s:2:\"30\";}');
INSERT INTO modoer_usergroups VALUES ('14','member','黄金会员','2000','','a:8:{s:16:\"member_forbidden\";s:1:\"0\";s:13:\"item_subjects\";s:0:\"\";s:12:\"item_reviews\";s:0:\"\";s:13:\"item_pictures\";s:0:\"\";s:15:\"comment_disable\";s:1:\"0\";s:16:\"exchange_disable\";s:1:\"0\";s:12:\"article_post\";s:1:\"1\";s:14:\"article_delete\";s:1:\"1\";}');
INSERT INTO modoer_usergroups VALUES ('15','special','VIP会员','0','#FF0000','a:5:{s:16:\"member_forbidden\";s:1:\"0\";s:13:\"item_subjects\";s:0:\"\";s:12:\"item_reviews\";s:0:\"\";s:13:\"item_pictures\";s:3:\"150\";s:15:\"comment_disable\";s:1:\"0\";}');

DROP TABLE IF EXISTS modoer_words;
CREATE TABLE modoer_words (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  keyword varchar(255) NOT NULL DEFAULT '',
  expression varchar(255) NOT NULL DEFAULT '',
  admin varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
) TYPE=MyISAM;