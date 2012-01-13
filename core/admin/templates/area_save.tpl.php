<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<script type="text/javascript">
loadscript('mdialog');
var maptext = '';
var point1 = point2 = '';
function map_mark(id, p1, p2) {
	maptext = id;
	point1 = p1;
	point2 = p2;
	var url = Url('modoer/index/act/map/width/450/height/300/p1/'+p1+'/p2/'+p2);
	if(point1 != '' && point2 != '') {
		url += '&show=yes';
	}
	var html = '<iframe src="' + url + '" frameborder="0" scrolling="no" width="450" height="310" id="ifupmap_map"></iframe>';
	html += '<button type="button" id="mapbtn1">标注坐标</button>&nbsp;';
	html += '<button type="button" id="mapbtn2">确定</button>';
	dlgOpen('选择地图坐标点', html, 470, 390);
	$('#mapbtn1').click(
		function() {
			$(document.getElementById('ifupmap_map').contentWindow.document.body).find('#markbtn').click();
		}
	);
	$('#mapbtn2').click(
		function() {
			point1 = $(document.getElementById('ifupmap_map').contentWindow.document.body).find('#point1').val();
			point2 = $(document.getElementById('ifupmap_map').contentWindow.document.body).find('#point2').val();
			if(point1 == '' || point2 == '') {
				alert('您尚未完成标注。');
				return;
			}
			$('#'+maptext).val(point1 + ',' + point2);
			dlgClose();
		}
	);
}
</script>
<div id="body">
    <div class="space">
        <div class="subtitle">操作提示</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                1. 添加地区先添加大市（例如：宁波），提交后，再下栏编辑地区内，添加省级以下的区/县（例如：海曙区），街道/路口（例如：镇明路）<br />
                2. 这里的地区按照大市，区/县，街道/路口来分成三级；<br />
                3. 第一次添加城市后，必须在“基本设置->界面和显示设置”页面选择默认点评城市；<br />
                4. <b class="font_1">本版本的系统不提供多城市的功能。</b>
        </td></tr>
        </table>
    </div>
    <div class="space">
    <form method="post" action="<?=cpurl($module, $act, $op)?>">
        <?if($op=='edit'):?>
        <input type="hidden" name="aid" value="<?=$aid?>" />
        <?else:?>
        <input type="hidden" name="area[pid]" value="<?=$pid?>" />
        <?endif;?>
        <div class="subtitle"><?=$op=='edit'?'编辑地区':'添加地区'?></div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="45%" class="altbg1"><strong>地区名称：</strong></td>
                <td width="*"><input type="text" name="area[name]" value="<?=$detail['name']?>" class="txtbox3" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>默认位置：</strong>地图标注时使用，用于确定城市或地区位置</td>
                <td><input type="text" name="area[mappoint]" id="mappoint" value="<?=$detail['mappoint']?>" class="txtbox3" />&nbsp;<button type="button" class="btn2" onclick="map_mark('mappoint','','');">选择坐标点</button></td>
            </tr>
            <tr id="remark_iframe" style="display:none;">
                <td colspan="2"><iframe src="#" id="imappoint" height="350" width="100%" style="border:0;"></iframe></td>
            </tr>
        </table>
        <center>
            <input type="submit" name="dosubmit" value=" 提交 " class="btn" />&nbsp;
            <input type="button" value=" 返回 " onclick="javascript:history.go(-1);" class="btn" />
        </center>
    </form>
    </div>
</div>