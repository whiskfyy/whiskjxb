<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<script type="text/javascript" src="./static/javascript/validator.js"></script>
<script type="text/javascript" src="./static/javascript/item.js"></script>
<script type="text/javascript" src="./static/javascript/product.js"></script>
<script type="text/javascript">
function checkform(obj) {
    return true;
}
var g;
function reload() {
    var obj = document.getElementById('reload');
    var btn = document.getElementById('switch');
    if(obj.innerHTML.match(/^<.+href=.+>/)) {
        g = obj.innerHTML;
        obj.innerHTML = '<input type="file" name="picture" size="20">';
        btn.innerHTML = '取消上传';
    } else {
        obj.innerHTML = g;
        btn.innerHTML = '重新上传';
    }
}
function product_create_category(sid) {
	if (!is_numeric(sid)) {
		alert('未选择产品主题。');
		return false;
	}
    var catname = prompt('请输入您的分类名称：','');
    if(!catname) return;
	$.post("<?=cpurl($module,$act,'create')?>", {'sid':sid, 'catname':catname, 'in_ajax':1 }, 
		function(result) {
		if (result.match(/\{\s+caption:".*",message:".*".*\s*\}/)) {
            myAlert(result);
        } else if(is_numeric(result)) {
			$("<option value='"+result+"'"+' selected="selected"'+">"+catname+"</option>").appendTo($('#catid'));
		} else {
		    alert('产品读取失败，可能网络忙碌，请稍后尝试。');
        }
	});
}
function select_subject(obj) {
	var url = "<?=cpurl($module,$act,'add')?>"+'&sid='+$(obj).attr('sid');
	document.location=url;
}
</script>
<div id="body">
<form method="post" action="<?=cpurl($module,$act,'save')?>" enctype="multipart/form-data" onsubmit="return validator(this);">
    <input type="hidden" name="sid" value="<?=$detail['sid']?>" />
    <div class="space">
        <div class="subtitle">编辑产品</div>
        <table width="95%" cellspacing="0" cellpadding="0" class="maintable">
            <tr id="tr_subject">
                <td width="100" class="altbg1" align="right"><span class="font_1">*</span>所属主题：</td>
                <td width="*">
					<div id="subject_search">
					<?if($subject):?>
					<a href="<?=url("item/detail/id/$sid")?>" target="_blank"><?=$subject['name'].($subject['subname']?"($subject[subname])":'')?></a>
					<?endif;?>
					</div>
					<script type="text/javascript">
						$('#subject_search').item_subject_search({
							input_class:'txtbox2',
							btn_class:'btn2',
							result_css:'item_search_result',
							result_click:select_subject,
							<?if($subject):?>
								sid:<?=$subject[sid]?>,
								current_ready:true,
							<?endif;?>
							<?if($op=='edit'):?>
								change_disable:true,
							<?endif;?>
							hide_keyword:true
						});
					</script>
				</td>
            </tr>
			<?if($subject):?>
            <tr id="tr_category">
                <td width="100" class="altbg1" align="right"><span class="font_1">*</span>选择分类：</td>
				<td><select name="catid" id="catid" validator="{'empty':'N','errmsg':'请选择产品分类。'}">
						<option value="" style="color:#CC0000">==选择产品分类==</option>
						<?=form_product_category($sid, $detail['catid']);?>
					</select>&nbsp;
					<button type="button" onclick="product_create_category(<?=$sid?>);" class="btn2">新建类别</button>
				</td>
            </tr>
            <tr>
                <td width="100" class="altbg1" align="right"><span class="font_1">*</span>产品名称：</td>
                <td width="*"><input type="text" name="subject" class="txtbox" size="40" value="<?=$detail['subject']?>" validator="{'empty':'N','errmsg':'请填写产品名称。'}" /></td>
            </tr>
            <tr>
                <td class="altbg1" align="right">封面图片：</td>
                <td>
                    <?if(!$detail['thumb']):?>
                    <input type="file" name="picture" size="20" />
                    <?else:?>
                    <span id="reload"><a href="<?=$detail['picture']?>" target="_blank" src="<?=$detail['thumb']?>" onmouseover="tip_start(this);"><?=$detail['thumb']?></a></span>&nbsp;&nbsp;[<a href="javascript:reload();" id="switch">重新上传</a>]
                    <?endif;?>
                </td>
            </tr>
            <tr>
                <td class="altbg1" align="right" valign="top">简单介绍：</td>
                <td><textarea name="description" style="width:500px;height:40px;"><?=$detail['description']?></textarea></td>
            </tr>
            <tr>
                <td class="altbg1" align="right">关闭评论：</td>
                <td><?=form_bool('closed_comment',$detail['closed_comment'])?></td>
            </tr>
            <?=$custom_form?>
			<?endif;?>
        </table>
        <center>
            <input type="hidden" name="do" value="<?=$op?>" />
            <input type="hidden" name="pid" value="<?=$_GET['pid']?>" />
            <input type="hidden" name="forward" value="<?=get_forward()?>" />
            <button type="submit" class="btn" name="dosubmit" value="yes">提交</button>&nbsp;
            <button type="button" class="btn" onclick="history.go(-1);">返回</button>&nbsp;
        </center>
    </div>
</form>
</div>