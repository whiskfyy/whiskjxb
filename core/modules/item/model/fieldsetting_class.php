<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$_G['loader']->model('fieldsetting', FALSE);
class msm_item_fieldsetting extends msm_fieldsetting {

	function __construct() {
		parent::__construct();
	}

    function msm_item_fieldsetting() {
		$this->__construct();
	}

	// 分类
	function _category() {
		$this->datatype = 'smallint(5)';
        $this->default = '0';
	}

	// 标签
	function _tag() {
		$this->datatype = 'varchar(255)';
        $this->default = '';
	}

	// 会员
	function _member() {
		$this->datatype = 'varchar(25)';
        $this->default = '';
	}

	// 状态
	function _status() {
		$this->datatype = 'tinyint(1)';
        $this->default = '0';
	}

	// 等级
	function _level() {
		$this->datatype = 'tinyint(3)';
        $this->default = '0';
	}

	// 地图
	function _mappoint() {
		$this->datatype = 'varchar(255)';
        $this->default = null;
	}

	// 模板
	function _template() {
		$this->datatype = 'smallint(5)';
        $this->default = '0';
	}

    // 视频
    function _video() {
        $this->datatype = 'varchar(255)';
        $this->default = '';
    }

	// 属性
	function _att() {
		$this->datatype = 'varchar(255)';
        $this->default = '';
	}
}

?>