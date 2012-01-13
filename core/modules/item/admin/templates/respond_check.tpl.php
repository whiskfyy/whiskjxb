<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<div id="body">
<form method="post" name="myform" action="<?=cpurl($module,$act,'',array('pid'=>$pid))?>">
    <div class="space">
        <div class="subtitle">审核信息</div>
        <ul class="cptab">
            <li<?=$act=='subject_check'?' class="selected"':''?>><a href="<?=cpurl($module,'subject_check')?>">审核主题</a></li>
            <li<?=$act=='review_check'?' class="selected"':''?>><a href="<?=cpurl($module,'review_check')?>">审核点评</a></li>
            <li<?=$act=='picture_check'?' class="selected"':''?>><a href="<?=cpurl($module,'picture_check')?>">审核图片</a></li>
            <li<?=$act=='respond_check'?' class="selected"':''?>><a href="<?=cpurl($module,'respond_check')?>">审核回应</a></li>
            <li<?=$act=='guestbook_check'?' class="selected"':''?>><a href="<?=cpurl($module,'guestbook_check')?>">审核留言</a></li>
        </ul>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
			<tr class="altbg1">
				<td width="25">选</td>
				<td width="180">点评标题</td>
				<td width="80">回应会员</td>
                <td width="*">回应内容</td>
                <td width="100">IP</td>
				<td width="110">回应时间</td>
			</tr>
			<?if($total && $list):?>
			<?while($val=$list->fetch_array()):?>
            <tr>
                <td><input type="checkbox" name="respondids[]" value="<?=$val['respondid']?>" /></td>
				<td><?if($val['sid']):?><a href="<?=url("item/detail/id/$val[sid]")?>#review" target="_blank"><?=$val['title']?$val['title']:'N/A'?></a><?else:?>点评信息不存在或已删除<?endif;?></td>
                <td><a href="<?=url("item/space/uid/$val[uid]")?>" target="_blank"><?=$val['username']?></a></td>
                <td><?=$val['content']?></td>
				<td><?=$val['ip']?></td>
				<td><?=date('Y-m-d H:i', $val['posttime'])?></td>
            </tr>
			<?endwhile;?>
			<tr class="altbg1">
				<td colspan="3" class="altbg1">
					<button type="button" onclick="checkbox_checked('respondids[]');" class="btn2">全选</button>
				</td>
				<td colspan="8" style="text-align:right;"><?=$multipage?></td>
			</tr>
			<?else:?>
			<tr>
				<td colspan="12">暂无信息。</td>
			</tr>
			<?endif;?>
        </table>
    </div>
	<?if($total):?>
    <div class="multipage"><?=$multipage?></div>
	<center>
		<input type="hidden" name="dosubmit" value="yes" />
		<input type="hidden" name="op" value="delete" />
		<button type="button" class="btn" onclick="easy_submit('myform','delete','respondids[]')">删除所选</button>&nbsp;
        <button type="button" class="btn" onclick="easy_submit('myform','checkup','respondids[]')">审核所选</button>&nbsp;
	</center>
	<?endif;?>
</form>
</div>