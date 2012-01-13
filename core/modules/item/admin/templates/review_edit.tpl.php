<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<div id="body">
<form method="post" name="myform" action="<?=cpurl($module,$act,$op,array('pid'=>$pid))?>">
    <?if($op=='report'):?>
    <input type="hidden" name="reportid" value="<?=$reportid?>" />
    <div class="space">
        <div class="subtitle">处理举报信息</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
            <?if($report['disposaltime']):?>
            <tr>
                <td class="altbg1" valign="top">处理时间：</td>
                <td><span class="font_3"><?=date('Y-m-d H:i', $report['disposaltime'])?></span></td>
            </tr>
            <?endif?>
            <tr>
                <td width="150" class="altbg1">违规类型：</td>
                <td width="*"><?=lang('item_report_sort_' . $report['sort'])?></td>
            </tr>
            <tr>
                <td class="altbg1" valign="top">补充内容：</td>
                <td><textarea style="width:500px;height:50px;" readonly><?=$report['reportcontent']?></textarea>
                <br /><span class="font_2">提交本表单，不会修改此处内容。</span></td>
            </tr>
            <?if(!$report['update_point'] && $report['uid'] > 0):?>
            <tr>
                <td class="altbg1">给会员加分：</td>
                <td><?=form_bool('report[update_point]', 1)?>&nbsp;<span class="font_2">会员:</span><a href="<?=url("space/index/uid/$report[uid]")?>" target="_blank"><?=$report['username']?></a></td>
            </tr>
            <?endif;?>
            <tr>
                <td class="altbg1" valign="top">处理说明：</td>
                <td><textarea name="report[reportremark]" style="width:500px;height:50px;"><?=$report['reportremark']?></textarea>
                <br /><span class="font_2">管理员处理本条信息的说明和备注。</span></td>
            </tr>
            <?if(!$report['disposaltime']):?>
            <tr>
                <td class="altbg1" valign="top">处理方式：</td>
                <td><input type="checkbox" name="report[delete]" id="report_delete" value="1" />
                <label for="report_delete">删除本条点评信息</label></td>
            </tr>
            <?endif;?>
        </table>
    </div>
    <?endif;?>

    <div class="space">
        <?if($detail):?>
        <div class="subtitle">编辑点评信息</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td align="right" class="altbg1" width="120px">主题名称：</td>
				<td width="*"><a href="<?=url("item/detail/id/$detail[sid]")?>" target="_blank"><?=$detail['title']?></a></td>
			</tr>
            <tr>
                <td align="right" class="altbg1">点评会员：</td>
                <td><a href="<?=url("space/index/suid/$detail[uid]")?>" target="_blank"><?=$detail['username']?></a></td></td>
            </tr>
            <tr>
                <td align="right" class="altbg1">IP地址：</td>
                <td><?=$detail['ip']?></td>
            </tr>
			<tr>
				<td align="right" class="altbg1" width="120px">点评标题：</td>
				<td width="*"><?=form_input('review[title]',$detail['title'],'txtbox2')?></td>
			</tr>
            <tr>
                <td align="right" class="altbg1">状态：<span class="font_1">*</span></td>
                <td><?=form_radio('review[status]', array(1=>'已审核',0=>'未审核'),$detail['status'])?></td>
            </tr>
			<tr>
				<td align="right" class="altbg1">评价分数：<span class="font_1">*</span></td>
				<td>
					<div style="width:600px;">
					<?foreach($review_opts as $key => $val):?>
					<select name="review[<?=$val['flag']?>]">
						<option value="">-<?=$val['name']?>-</option>
						<option value="0"<?=!$detail[$val['flag']]?' selected':''?>>差</option>
						<option value="1"<?=$detail[$val['flag']]==1?' selected':''?>>一般</option>
						<option value="2"<?=$detail[$val['flag']]==2?' selected':''?>>还好</option>
						<option value="3"<?=$detail[$val['flag']]==3?' selected':''?>>好</option>
						<option value="4"<?=$detail[$val['flag']]==4?' selected':''?>>很好</option>
						<option value="5"<?=$detail[$val['flag']]==5?' selected':''?>>非常好</option>
					</select>
					<?endforeach;?>
					</div>
				</td>
			</tr>
			<?if($catcfg['useprice']):?>
            <tr>
                <td align="right" class="altbg1"><?=$catcfg['useprice_title']?>：<?if($catcfg['useprice_required']):?><span class="font_1">*</span><?endif;?></td>
                <td><input type="text" name="review[price]" class="t_input" value="<?=$detail['price']?>" />&nbsp;&nbsp;<?=$catcfg['useprice_unit']?></td>
            </tr>
			<?endif;?>
            <tr>
                <td align="right" class="altbg1">喜欢程度：<span class="font_1">*</span></td>
                <td><?=form_radio('review[enjoy]', array(0=>'不喜欢',1=>'无所谓',2=>'喜欢',3=>'很喜欢'), $detail['enjoy'])?></td>
            </tr>
            <?foreach($catcfg['taggroup'] as $val):?>
            <tr>
                <td align="right" class="altbg1"><?=$taggroups[$val]['name']?>：</td>
                <?$detail_tags = $detail['taggroup'] ? @unserialize($detail['taggroup']) : array();?>
                <td>
                    <?if($taggroups[$val]['sort']==1):?>
                    <input type="text" name="review[taggroup][<?=$val?>]" id="taggroup_<?=$val?>" size="50" class="t_input" value="<?=@implode(',',$detail_tags[$val])?>" />&nbsp;多个标签请用逗号","分开
                    <?elseif($taggroups[$val]['sort']==2):?>
                    <?$tagconfig = explode(',', $taggroups[$val]['options']);?>
                    <?foreach($tagconfig as $ky => $tgval):?>
                    <input type="checkbox" name="review[taggroup][<?=$val?>][]" value="<?=$tgval?>"<?if(@in_array($tgval,$detail_tags[$val])):?> checked<?endif;?> id="taggroup_<?=$val.'_'.$ky?>" /><label for="taggroup_<?=$val.'_'.$ky?>"><?=$tgval?></label>&nbsp;
                    <?endforeach;?>
                    <?endif;?>
                </td>
            </tr>
            <?endforeach;?>
            <tr>
                <td align="right" valign="top" class="altbg1">点评内容：<span class="font_1">*</span></td>
                <td>
					<textarea name="review[content]" style="width:90%;height:120px;padding:5px;"><?=$detail['content']?></textarea>
					<div class="font_1">请将点评内容限制在 <?=$MOD['review_min']?> - <?=$MOD['review_max']?> 个字符以内。</div>
				</td>
            </tr>
        </table>
        <?else:?>
        <input type="hidden" name="empty_review" value="yes" />
        <p style="font-size:14px;font-weight:bold;">点评信息不存在或者已删除。</p>
        <?endif;?>
    </div>
	<center>
        <input type="hidden" name="forward" value="<?=get_forward()?>" />
		<input type="hidden" name="review[sid]" value="<?=$detail['sid']?>" />
		<input type="hidden" name="rid" value="<?=$rid?>" />
		<?=form_submit('dosubmit',lang('global_submit'),'yes','btn')?>&nbsp;<?=form_button_return(lang('global_return'),'btn')?>
	</center>
</form>
</div>