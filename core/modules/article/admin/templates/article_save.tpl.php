<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<script type="text/javascript" src="./data/cachefiles/article_category.js?r=<?=$MOD[jscache_flag]?>"></script>
<script type="text/javascript" src="./static/javascript/article.js"></script>
<script type="text/javascript" src="./static/javascript/item.js"></script>
<script type="text/javascript">
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

window.onload = function() {
    <?if(!$detail['catid']):?>article_select_category(document.getElementById('pid'),'catid');<?endif;?>
}
</script>
<div id="body">
<form method="post" action="<?=cpurl($module,$act,'save')?>" enctype="multipart/form-data">
    <div class="space">
        <div class="subtitle">增加/编辑文章</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0" id="config1" mousemove='N'>
            <tr>
                <td class="altbg1" width="15%"><strong>文章名称:</strong>不能超过60个字符，必填</td>
                <td width="*"><input type="text" name="subject" class="txtbox" value="<?=$detail['subject']?>" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>分类:</strong>选择文章分类，必须选择2级分类，必填</td>
                <td>
                    <select name="pid" id="pid" style="width:200px;" onchange="article_select_category(this,'catid');">
                        <?=form_article_category(0,$detail['catid']);?>
                    </select>&nbsp;
                    <select name="catid" id="catid" style="width:200px;">
                        <?=$detail['catid']?form_article_category($detail['catid']):''?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="altbg1"><strong>文章封面:</strong>非必填</td>
                <td>
                    <?if(!$detail['thumb']):?>
                    <input type="file" name="picture" size="20" />
                    <?else:?>
                    <span id="reload"><a href="<?=$detail['picture']?>" target="_blank" src="<?=$detail['thumb']?>" onmouseover="tip_start(this);"><?=$detail['thumb']?></a></span>&nbsp;
                    [<a href="javascript:reload();" id="switch">重新上传</a>]
                    <?endif;?>
                </td>
            </tr>
            <tr>
                <td class="altbg1"><strong>审核状态:</strong>未审核则不显示在前台，需要在后台审核，必填</td>
                <td><?=form_bool('status',!$detail || $detail['status'] ? 1 : 0)?></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>关闭评论:</strong>使用评论模块进行文章评论，必填</td>
                <td><?=form_bool('closed_comment',$detail['closed_comment'])?></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>自定义属性:</strong>由0-255数字组成，非必填</td>
                <td>
                    <input type="text" name="att" id="att" class="txtbox4" value="<?=$detail['att']?>" />
                    <select id="att_select" onchange="$('#att').val($('#att_select').val());">
                        <option value="0">=选择属性=</option>
                        <?=form_article_att($detail['att'])?>
                    </select>
                </td>
            </tr>
            <tr><input type="hidden" name="sid" id="sid" value="<?=$detail['sid']?>" />
                <td class="altbg1"><strong>关联主题:</strong>输入关键字搜索主题，选择一个管理的主题，非必填</td>
                <td>
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
							<?if($subject):?>
								sid:<?=$subject[sid]?>,
								current_ready:true,
							<?endif;?>
							hide_keyword:true
						});
					</script>
                </td>
            </tr>
            <tr>
                <td class="altbg1"><strong>作者:</strong>必填项 </td>
                <td><input type="text" name="author" class="txtbox3" value="<?=$detail['author']?$detail['author']:$admin->adminname?>" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>来源:</strong>非必填</td>
                <td><input type="text" name="copyfrom" class="txtbox" value="<?=$detail['copyfrom']?>" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>关键字:</strong>多个关键字，请用逗号分隔，非必填</td>
                <td><input type="text" name="keywords" class="txtbox" value="<?=$detail['keywords']?>" /></td>
            </tr>
            <tr>
                <td class="altbg1" valign="top"><strong>简介:</strong>纯文字，字数在10-255字符之间，必填</td>
                <td><textarea name="introduce" style="width:99%;height:80px;"><?=$detail['introduce']?></textarea></td>
            </tr>
            <tr>
                <td class="altbg1" valign="top"><strong>介绍:</strong>字数控制在10-5000个字符，必填</td>
                <td><?=$edit_html?></td>
            </tr>
        </table>
    </div>
    <?if($op=='edit'):?>
    <input type="hidden" name="articleid" value="<?=$detail['articleid']?>" />
    <?endif;?>
    <input type="hidden" name="do" value="<?=$op?>" />
    <input type="hidden" name="forward" value="<?=get_forward()?>" />
    <center>
        <input type="submit" name="dosubmit" class="btn" value="提交表单" onclick="KE.util.setData('content');" />&nbsp;
        <?=form_button_return(lang('admincp_return'),'btn')?>
    </center>
</form>
</div>