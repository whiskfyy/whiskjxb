<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<div id="body">
<form method="post" action="<?=cpurl($module,$act,'post')?>">
    <input type="hidden" name="uid" value="<?=$_GET['uid']?>" />
    <div class="space">
        <div class="subtitle">用户资料修改</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
            <tr class="altbg2"><td colspan="2"><strong>基本信息</strong></td></tr>
            <tr>
                <td class="altbg1" width="150">用户名:</td>
                <td width="*"><?=$detail['username']?></td>
            </tr>
            <tr>
                <td class="altbg1">E-mail:</td>
                <td><input type="text" name="member[email]" value="<?=$detail['email']?>" class="txtbox2" /></td>
            </tr>
            <tr>
                <td class="altbg1">用户组:</td>
                <?php $gid = $UP->point_by_usergroup($detail['point']);?>
                <td><select name="member[groupid]">
                    <?=form_member_usergroup($detail['groupid'],array('system','special'))?>
                    <option value="<?=$gid?>"<?if($gid==$detail['groupid'])echo' selected="selected"';?>><?=template_print('member','group',array('groupid'=>$gid))?></option>
                </select>&nbsp;<span class="font_2">普通用户组会随着积分自动变化，特殊和系统用户组不会。</span></td>
            </tr>
            <tr>
                <td class="altbg1">会员组到期时间:</td>
                <td><input type="text" name="member[nexttime]" value="<?=$detail['nexttime']?date('Y-m-d', $detail['nexttime']):''?>" />&nbsp;YYYY-MM-DD<span class="font_2">仅特殊用户组有效</span></td>
            </tr>
            <tr>
                <td class="altbg1">到期后转入组:</td>
                <td><select name="member[nextgroupid]">
                    <option value="10">普通用户</option>
                    <?=form_member_usergroup($detail['nextgroupid'],array('system','special'))?>
                </select></td>
            </tr>
            <tr class="altbg2"><td colspan="2"><strong>积分情况</strong></td></tr>
            <tr>
                <td class="altbg1">积分:</td>
                <td><input type="text" name="member[point]" value="<?=$detail['point']?>" class="txtbox3" /></td>
            </tr>
            <tr>
                <td class="altbg1">金币:</td>
                <td><input type="text" name="member[coin]" value="<?=$detail['coin']?>" class="txtbox3" /></td>
            </tr>
            <tr class="altbg2"><td colspan="2"><strong>密码修改</strong></td></tr>
            <tr>
                <td class="altbg1">新密码:</td>
                <td><input type="password" name="member[password]" class="txtbox2" />&nbsp;&nbsp;不修改，请留空</td>
            </tr>
            <tr>
                <td class="altbg1">再次输入密码:</td>
                <td><input type="password" name="member[password2]" class="txtbox2" /></td>
            </tr>
        </table>
    </div>
    <center>
        <input type="hidden" name="forward" value="<?=get_forward()?>" />
        <button type="submit" name="dosubmit" value="yes" class="btn">提交</button>&nbsp;
        <button type="button" onclick="history.go(-1);" class="btn">返回</button>
    </center>
</form>
</div>