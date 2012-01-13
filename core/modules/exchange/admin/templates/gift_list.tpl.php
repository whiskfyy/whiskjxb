<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<div id="body">
<form method="post" name="myform" action="<?=cpurl($module,$act)?>&">
    <div class="space">
        <div class="subtitle">礼品管理</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0" id="config1" trmouse="Y">
			<tr class="altbg1">
				<td width="25">选</td>
                <td width="60">排序</td>
                <td width="30">可用</td>
                <td width="*">名称</td>
				<td width="80">价格</td>
				<td width="60">库存</td>
				<td width="60">已兑换</td>
                <td width="60">操作</td>
			</tr>
            <?if($total):?>
            <?while($val = $list->fetch_array()):?>
            <tr>
                <td><input type="checkbox" name="giftids[]" value="<?=$val['giftid']?>" /></td>
                <td><input type="text" name="gifts[<?=$val['giftid']?>][displayorder]" value="<?=$val['displayorder']?>" class="txtbox5" /></td>
                <td><input type="checkbox" name="gifts[<?=$val['giftid']?>][available]" value="1"<?if($val['available'])echo' checked="checked"';?> /></td>
                <td><a href="<?=url("exchange/gift/id/$val[giftid]")?>" target="_blank"><?=$val['name']?></a></td>
                <td><?=$val['price']?></td>
                <td><?=$val['num']?></td>
                <td><?=$val['salevolume']?></td>
                <td><a href="<?=cpurl($module,'gift','edit',array('giftid'=>$val['giftid']))?>">编辑</a></td>
            </tr>
            <?endwhile;?>
			<tr class="altbg1">
				<td colspan="3" class="altbg1">
					<button type="button" onclick="checkbox_checked('giftids[]');" class="btn2">全选</button>
				</td>
				<td colspan="10" style="text-align:right;"><?=$multipage?></td>
			</tr>
            <?else:?>
            <td colspan="10">暂无信息。</td>
            <?endif;?>
        </table>
    </div>
	<center>
        <?if($total):?>
		<input type="hidden" name="dosubmit" value="yes" />
		<input type="hidden" name="op" value="delete" />
		<button type="button" class="btn" onclick="easy_submit('myform','update',null)">更新信息</button>
        <button type="button" class="btn" onclick="easy_submit('myform','delete','giftids[]')">删除所选</button>
        <?endif;?>
        <button type="button" class="btn" onclick="document.location='<?=cpurl($module,$act,'add')?>'">增加礼品</button>
	</center>
</form>
</div>