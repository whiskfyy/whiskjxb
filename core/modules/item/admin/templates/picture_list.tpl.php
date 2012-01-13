<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<div id="body">
<form method="post" name="myform" action="<?=cpurl($module,$act,'',array('pid'=>$pid))?>">
    <div class="space">
        <div class="subtitle">图片管理</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0" trmouse="Y">
            <tr class="altbg2"><th colspan="9">
                <ul class="subtab">
                    <?foreach($_G['loader']->variable('category',MOD_FLAG) as $key => $val) { if($val['pid']) continue; ?>
                    <li<?=$pid==$key?' class="current"':''?>><a href="<?=cpurl($module,$act,'',array('pid'=>$key))?>"><?=$val['name']?></a></li>
                    <?}?>
                </ul>
            </th></tr>
            <tr class="altbg1">
                <td width="25">选?</td>
                <td width="105">图片</td>
				<td width="*">标题与说明</td>
                <td width="80">尺寸/大小</td>
                <td width="80">上传会员</td>
                <td width="150"><?=$model['item_name']?>名称/上传时间</td>
            </tr>
			<?if($total):?>
			<?while($val=$list->fetch_array()):?>
            <tr>
                <td><input type="checkbox" name="picids[]" value="<?=$val['picid']?>" /></td>
                <td class="picthumb"><a href="<?=$val['filename']?>" target="_blank"><img src="<?=$val['thumb']?>" /></a></td>
                <td>
					<div style="margin-bottom:10px;">标题：<input type="text" class="txtbox2" name="picture[<?=$val['picid']?>][title]" value="<?=$val['title']?>"  /></div>
					<div>说明：<input type="text" class="txtbox2" name="picture[<?=$val['picid']?>][comments]" value="<?=$val['comments']?>" /></div>
                </td>
                <td><?=round($val['size']/1024)?> KB<br /><?=$val['width']?> × <?=$val['height']?></td>
                <td><img src="<?=get_face($val['uid'])?>" /><br /><a href="<?=url("space/index/suid/$val[uid]")?>" target="_blank"><?=$val['username']?></a></td>
                <td><a href="<?=url("item/detail/id/$val[sid]")?>" target="_blank"><?=$val['name'].'&nbsp;'.$val['subname']?></a><br /><?=date('Y-m-d H:i', $val['addtime'])?></td>
            </tr>
			<?endwhile;?>
			<tr class="altbg1">
				<td colspan="2"><button type="button" onclick="checkbox_checked('picids[]');" class="btn2">全选</button></td>
				<td colspan="4" style="text-align:right;"><?=$multipage?></td>
			</tr>
			<?else:?>
			<tr>
				<td colspan="6">暂无信息。</td>
			</tr>
			<?endif;?>
        </table>
    </div>
	<?if($total):?>
    <div class="multipage"><?=$multipage?></div>
	<center>
		<input type="hidden" name="dosubmit" value="yes" />
		<input type="hidden" name="op" value="checkup" />
		<button type="button" class="btn" onclick="easy_submit('myform','update',null)">更新修改</button>&nbsp;
		<button type="button" class="btn" onclick="easy_submit('myform','delete','picids[]')">删除所选</button>
	</center>
	<?endif;?>
</form>
</div>