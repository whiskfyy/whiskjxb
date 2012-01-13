DROP TABLE IF EXISTS modoer_exchange_log;
CREATE TABLE modoer_exchange_log (
  exchangeid mediumint(8) unsigned NOT NULL auto_increment,
  uid mediumint(8) unsigned NOT NULL default '0',
  giftid mediumint(8) unsigned NOT NULL default '0',
  giftname varchar(200) NOT NULL default '',
  price int(10) unsigned NOT NULL default '0',
  number int(10) unsigned NOT NULL default '1',
  status tinyint(1) NOT NULL default '1',
  status_extra varchar(255) NOT NULL default '',
  exchangetime int(10) NOT NULL default '0',
  contact mediumtext NOT NULL,
  checker varchar(20) NOT NULL default '',
  PRIMARY KEY  (exchangeid),
  KEY uid (uid),
  KEY giftid (giftid)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_exchange_gifts;
CREATE TABLE modoer_exchange_gifts (
  giftid mediumint(8) unsigned NOT NULL auto_increment,
  name varchar(200) NOT NULL default '',
  sort tinyint(1) unsigned NOT NULL DEFAULT '1',
  available tinyint(1) NOT NULL default '0',
  displayorder tinyint(3) NOT NULL default '0',
  description text NOT NULL,
  price int(10) unsigned NOT NULL default '0',
  num int(10) unsigned NOT NULL default '0',
  thumb varchar(255) NOT NULL default '',
  picture varchar(255) NOT NULL default '',
  salevolume int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (giftid),
  KEY available (available)
) TYPE=MyISAM;

DROP TABLE IF EXISTS modoer_exchange_serial;
CREATE TABLE IF NOT EXISTS modoer_exchange_serial (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `giftid` mediumint(8) unsigned NOT NULL default '0',
  `serial` varchar(255) NOT NULL default '',
  `status` tinyint(1) unsigned NOT NULL default '1',
  `dateline` int(10) unsigned NOT NULL default '0',
  `sendtime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `giftid` (`giftid`)
) TYPE=MyISAM;
