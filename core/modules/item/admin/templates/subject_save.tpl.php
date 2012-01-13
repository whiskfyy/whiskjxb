<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<script type="text/javascript" src="./static/javascript/validator.js"></script>
<script type="text/javascript" src="./static/javascript/item.js"></script>
<script type="text/javascript">
loadscript('mdialog');
loadscript('item');

function select_category() {
    var pid = $('#pid').val();
    if(pid=='') return;
    document.location = '<?=cpurl($module,$act)?>&pid='+pid;
}

var maptext = '';
var point1 = point2 = '';
function map_mark(id, p1, p2) {
	var width = 600;
	var height = 350;
	maptext = id;
	point1 = p1;
	point2 = p2;
    var url = Url('modoer/index/act/map/width/'+width+'/height/'+height+'/p1/'+p1+'/p2/'+p2);
	if(point1 != '' && point2 != '') {
		url += '&show=yes';
	}
	if($('#area_1').val()) {
		url += '&city_id=' + $('#area_1').val();
	}
	var html = '<iframe src="' + url + '" frameborder="0" scrolling="no" width="'+width+'" height="'+(height+10)+'" id="ifupmap_map"></iframe>';
	html += '<button type="button" id="mapbtn1">标注坐标</button>&nbsp;';
	html += '<button type="button" id="mapbtn2">确定</button>';
	dlgOpen('选择地图坐标点', html, width+20, height+80);
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

function copy_to_mappoint() {
    var point = $('#up_mappoint').val();
    $('#mappoint_mappoint').val(point).css('background','#FFFF99');
    msgOpen('操作完毕，坐标已复制。');
}

function map_show() {
    var point = $('#up_mappoint').val();
    var point = point.split(',');
    var url = Url('modoer/index/act/map/width/450/height/300/p1/'+point[0]+'/p2/'+point[1]+'/show/yes/title/纠错坐标点');
	var html = '<iframe src="' + url + '" frameborder="0" scrolling="no" width="450" height="310"></iframe>';
    dlgOpen('查看纠错坐标点', html, 470, 350);
}

function insert_upload_pic(sid) {
    select_subject_thumb(sid,1);
}

function upload_subject_thumb(sid) {
    if(!is_numeric(sid)) { alert('无效的SID.'); return; }
    var html = '<form name="frmupload" id="frmupload" method="post" action="<?=cpurl($module,"picture_list","upload",array("in_ajax"=>"1"))?>" enctype="multipart/form-data"><input type="hidden" name="sid" value="'+sid+'" /><input type="file" name="picture" />&nbsp;<input type="button" value=" 上传 " onclick="ajaxPost(\'frmupload\',\''+sid+'\',\'insert_upload_pic\');" /></form>';
    dlgOpen('上传图片', html, 300, 100);
}
</script>
<div id="body">
<form method="post" action="<?=cpurl($module,$act,$op)?>&" enctype="multipart/form-data" onsubmit="return validator(this);">
    <input type="hidden" name="forward" value="<?=get_forward()?>" />
    <?if($op=='log'):?>
    <input type="hidden" name="upid" value="<?=$upid?>" />
    <div class="space">
        <div class="subtitle">处理补充信息</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
            <?if($log['disposaltime']):?>
            <tr>
                <td class="altbg1" valign="top">处理时间：</td>
                <td><span class="font_3"><?=date('Y-m-d H:i', $log['disposaltime'])?></span></td>
            </tr>
            <?endif?>
            <tr>
                <td width="150" class="altbg1">补充类型：</td>
                <td width="*"><?=$log['ismappoint']?'地图报错':'补充信息'?></td>
            </tr>
            <?if(!$log['ismappoint']):?>
            <tr>
                <td class="altbg1" valign="top">补充内容：</td>
                <td><textarea style="width:500px;height:50px;" readonly><?=$log['upcontent']?></textarea>
                <br /><span class="font_2">提交本表单，不会修改此处内容。</span></td>
            </tr>
            <?else:?>
            <tr>
                <td class="altbg1">纠错坐标：</td>
                <td><input type="text" class="txtbox2" id="up_mappoint" value="<?=trim($log['upcontent'])?>" readonly />&nbsp;
                <input type="button" class="btn2" value="查看坐标点" onclick="map_show();" />&nbsp;
                <input type="button" class="btn2" value="替换坐标点" onclick="copy_to_mappoint();" />
                <br /><span class="font_2">请将上面的地图坐标复制到最下方的地图坐标完成坐标更新。</span></td>
            </tr>
            <?endif;?>
            <?if(!$log['update_point'] && $log['uid'] > 0):?>
            <tr>
                <td class="altbg1">给会员加分：</td>
                <td><?=form_bool('log[update_point]', 1)?>&nbsp;<span class="font_2">会员:</span><a href="<?=url("space/index/uid/$log[uid]")?>" target="_blank"><?=$log['username']?></a></td>
            </tr>
            <?endif;?>
            <tr>
                <td class="altbg1" valign="top">处理说明：</td>
                <td><textarea name="log[upremark]" style="width:500px;height:50px;"><?=$log['upremark']?></textarea>
                <br /><span class="font_2">管理员处理本条信息的说明和备注。</span></td>
            </tr>
        </table>
    </div>
    <?endif;?>
    <div class="space">
        <div class="subtitle"><?=$sid?'编辑':'添加'?>主题(点评对象)</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="150" class="altbg1" align="right"><span class="font_1">*</span>主题分类：</td>
                <td width="*">
                    <select name="pid" id="pid" onchange="select_category();"<?=$sid>0?' disabled':''?>>
                        <option value="">==选择分类==</option>
						<?=form_item_category_main($pid)?>
                    </select>
                </td>
                <td>&nbsp;</td>
            </tr>
            <?if($pid):?>
            <?if($MOD['sldomain']):?>
            <tr>
                <td width="150" class="altbg1" align="right">二/三级域名/个性目录：</td>
                <td width="*"><input type="text" class="txtbox4" name="t_item[domain]" value="<?=$detail['domain']?>" />&nbsp;<span class="font_2">由字母(a-z)和数字(0-9)组成，不能使用纯数字，限制20个字符。</span></td>
                <td>&nbsp;</td>
            </tr>
            <?endif;?>
			<?if($detail):?>
            <tr>
                <td width="150" class="altbg1" align='right'>选择封面：</td>
                <td width="*">
                    <input type="text" class="txtbox" name="t_item[thumb]" id="thumb" value="<?=$detail['thumb']?>" />&nbsp;
                    <button type="button" class="btn2" onclick="javascript:select_subject_thumb(<?=$sid?>,1);">选择</button>&nbsp;
                    <button type="button" class="btn2" onclick="javascript:upload_subject_thumb(<?=$sid?>);">上传</button>
                </td>
                <td>&nbsp;</td>
            </tr>
            <?elseif($pid>0):?>
            <tr>
                <td width="150" class="altbg1" align='right'>选择封面：</td>
                <td><input type="file" name="picture" /></td>
            </tr>
            <?endif;?>
            <tr>
                <td class="altbg1" align="right">管理员：</td>
                <td>
                    <?if($detail):?>
                    <?if($detail['owner']):?>
                    <a href="<?=cpurl($module,$act,'owner',array('sid'=>$detail['sid']))?>"><?=$detail['owner']?></a>
                    <?else:?>
                    <a href="<?=cpurl($module,$act,'owner',array('sid'=>$detail['sid']))?>">无 / 添加管理员</a>
                    <?endif;?>
                    <?else:?>
                    <input type="text" name="t_item[owner]" value="<?=$detail['owner']?>" class="txtbox3" />
                    <?endif;?>
                </td>
            </tr>
            <?=$field_form?>
            <?endif;?>
        </table>
        <?if($pid):?>
        <center>
            <?if($act=='subject_edit'):?>
			<input type="hidden" name="sid" value="<?=$sid?>" />
            <?endif;?>
			<?=form_submit('dosubmit',lang('global_submit'),'yes','btn')?>
			<?=$sid?'&nbsp;&nbsp;'.form_button_return(lang('global_return'),'btn'):''?>
		</center>
        <?endif;?>
    </div>
</form>
</div>