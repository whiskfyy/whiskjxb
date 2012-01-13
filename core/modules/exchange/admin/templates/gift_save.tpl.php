<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<script type="text/javascript" src="./static/javascript/validator.js"></script> 
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
</script>
<div id="body">
<form method="post" action="<?=cpurl($module,$act,'save')?>" enctype="multipart/form-data">
    <div class="space">
        <div class="subtitle">增加/编辑礼品</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0" id="config1">
            <tr>
                <td class="altbg1" width="120">礼品名称:</td>
                <td width="*"><input type="text" name="name" class="txtbox" value="<?=$detail['name']?>" /></td>
            </tr>
            <tr>
                <td class="altbg1">排序:</td>
                <td><input type="text" name="displayorder" class="txtbox4" value="<?=$detail['displayorder']?>" /></td>
            </tr>
            <tr>
                <td class="altbg1">可用:</td>
                <td><?=form_bool('available',$detail['available']?$detail['available']:1)?></td>
            </tr>
            <tr>
                <td class="altbg1">价格:</td>
                <td><input type="text" name="price" class="txtbox3" value="<?=$detail['price']?>" /></td>
            </tr>
            <tr>
                <td class="altbg1">库存:</td>
                <td><input type="text" name="num" class="txtbox3" value="<?=$detail['num']?>" /></td>
            </tr>
            <tr>
                <td class="altbg1">图片：</td>
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
                <td class="altbg1">介绍:</td>
                <td><?=$edit_html?></td>
            </tr>
        </table>
    </div>
    <?if($op=='edit'):?>
    <input type="hidden" name="giftid" value="<?=$detail['giftid']?>" />
    <?endif;?>
    <input type="hidden" name="do" value="<?=$op?>" />
    <input type="hidden" name="forward" value="<?=get_forward()?>" />
    <center><?=form_submit('dosubmit',lang('admincp_submit'),'yes','btn')?>&nbsp;
    <?=form_button_return(lang('admincp_return'),'btn')?></center>
</form>
</div>