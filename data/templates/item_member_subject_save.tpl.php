<? !defined('IN_MUDDER') && exit('Access Denied'); include template('modoer_header'); ?>
<script type="text/javascript" src="<?=URLROOT?>/static/javascript/validator.js"></script>
<script type="text/javascript">
function select_category() {
    var pid = $('#pid').val();
    if(pid == '') return;
    document.location =  Url('item/member/ac/<?=$ac?>/pid/'+pid);
}

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
    <div class="myhead"></div>
    <div class="mymiddle">
        <div class="myleft">
            
<? include template('menu','member','member'); ?>
        </div>
        <div class="myright">
            <div class="myright_top"></div>
            <div class="myright_middle">
                <h3><? if($op=='edit') { ?>
编辑主题
<? } else { ?>
添加主题<? } ?>
</h3>
                <div class="mainrail">
                    <form method="post" name="myform" action="<?php echo url("item/member/ac/{$ac}/rand/{$_G['random']}"); ?>" enctype="multipart/form-data" onsubmit="return validator(this);">
                    <table width="100%" cellspacing="0" cellpadding="0" class="maintable">
                        <tr>
                            <td width="100" align="right">主题分类：</td>
                            <td width="*">
                                <select name="pid" id="pid" onchange="select_category();"<? if($ac=='subject_edit') { ?>
disabled="disabled"<? } ?>
>
                                    <option value="">==选择主分类==</option>
                                    <?php echo form_item_category_main($pid); ?>                                </select>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <? if($ac == 'subject_edit' && $detail) { ?>
                        <tr>
                            <td align="right">选择封面：</td>
                            <td>
                                <input type="text" class="t_input" size="40" name="t_item[thumb]" id="thumb" value="<?=$detail['thumb']?>" />&nbsp;
                                <button type="button" class="btn2" onclick="javascript:select_subject_thumb(<?=$sid?>,1);">选择</button>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        
<? } elseif($pid>0) { ?>
                        <tr>
                            <td align="right">上传封面：</td>
                            <td>
                                <input type="file" name="picture" />
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <? } ?>
                        <?=$field_form?>
                        <? if($ac == 'subject_add' && $pid > 0 && $MOD['seccode_subject']) { ?>
                        <tr>
                            <td align="right"><span class="font_1">*</span>验证码：</td>
                            <td><div id="seccode"></div><input type="text" name="seccode" onfocus="show_seccode();" class="t_input" validator="{'empty':'N','errmsg':'请填写验证码。'}" /></td>
                        </tr>
                        <? } ?>
                    </table>
                    <div class="text_center">
                        <? if($ac == 'subject_edit') { ?>
                        <input type="hidden" name="sid" value="<?=$sid?>" />
                        <input type="hidden" name="forward" value="<?php echo get_forward(); ?>" />
                        <? } ?>
                        <? if($pid) { ?>
                        <button type="submit" name="dosubmit" value="yes">提交</button>&nbsp;&nbsp;
                        <button type="reset">重置</button>&nbsp;&nbsp;
                        <? } ?>
                        <button type="button" onclick="history.go(-1);">返回</button>&nbsp;&nbsp;
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="mybottom"></div>
</div><?php footer(); ?>