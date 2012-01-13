<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<div id="body">
<div class="space">
    <div class="subtitle">操作提示</div>
    <table class="maintable" border="0" cellspacing="0" cellpadding="0">
    <tr><td>积分中的增加事件行为，有对应的删除事件行为，删除事件所减少的积分与增加的积分相等。</td></tr>
    </table>
</div>
<?=form_begin(cpurl($module,$act,'post'))?>
    <div class="space">
        <div class="subtitle">积分设置</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
            <tr class="altbg1">
                <td width="250">事件</td>
                <td width="100">积分</td>
                <td width="*">金币</td>
            </tr>
            <?foreach($list as $key => $val) {?>
            <tr>
                <td class="altbg1"><?=$val?></td>
                <td><input type="text" name="point[<?=$key?>][point]" value="<?=$point[$key]['point']?>" class="txtbox4" /></td>
                <td><input type="text" name="point[<?=$key?>][coin]" value="<?=$point[$key]['coin']?>" class="txtbox4" /></td>
            </tr>
            <?}?>
        </table>
    </div>
    <center><?=form_submit('dosubmit',lang('admincp_submit'),'yes','btn')?></center>
<?=form_end()?>
</div>