<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<div id="body">
<form method="post" name="myform" action="<?=cpurl($module,$act,'',array('pid'=>$pid))?>">
    <div class="space">
        <div class="subtitle">点评信息管理</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0" trmouse="Y">
            <tr class="altbg2"><th colspan="12">
                <ul class="subtab">
                    <?foreach($_G['loader']->variable('category',MOD_FLAG) as $key => $val) { if($val['pid']) continue; ?>
                    <li<?=$pid==$key?' class="current"':''?>><a href="<?=cpurl($module,$act,'',array('pid'=>$key))?>"><?=$val['name']?></a></li>
                    <?}?>
                </ul>
            </th></tr>
			<tr class="altbg1">
				<td width="25">删?</td>
				<td width="50">作者</td>
				<td width="160">点评标题</td>
				<td width="*">点评内容</td>
				<td width="30">鲜花</td>
				<td width="30">回应</td>
                <td width="90">IP</td>
				<td width="110">点评时间</td>
				<td width="50">编辑</td>
			</tr>
			<?if($total && $list):?>
			<?while($val=$list->fetch_array()):?>
            <tr>
                <td><input type="checkbox" name="rids[]" value="<?=$val['rid']?>" /></td>
				<td><a href="<?=url("space/index/uid/$val[uid]")?>" target="_blank"><img src="<?=get_face($val['uid'])?>" /></a><br /><?=$val['username']?></td>
                <td><a href="<?=url("item/detail/id/$val[sid]")?>" target="_blank"><?=$val['title']?></a></td>
				<td><?=trimmed_title($val['content'], 50, '...')?></td>
				<td><?=$val['flowers']?></td>
				<td><a href="<?=cpurl($module,'respond_list','',array('rid' => $val['rid']))?>"><?=$val['responds']?></a></td>
				<td><?=$val['ip']?></td>
                <td><?=date('Y-m-d H:i', $val['posttime'])?></td>
				<td><a href="<?=cpurl($module, 'review_edit', '', array('rid' => $val['rid']))?>">编辑</a></td>
            </tr>
			<?endwhile;?>
			<tr class="altbg1">
				<td colspan="3" class="altbg1">
					<button type="button" onclick="checkbox_checked('rids[]');" class="btn2">全选</button>
					<input type="checkbox" name="delete_point" id="delete_point" value="1" /><label for="delete_point">删除的同时减少作者积分</label>
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
		<button type="button" class="btn" onclick="easy_submit('myform','delete','rids[]')">删除所选</button>
	</center>
	<?endif;?>
</form>
</div>