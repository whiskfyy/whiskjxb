<? !defined('IN_MUDDER') && exit('Access Denied'); include template('modoer_header'); ?>
<div id="body">
    <div class="myhead"></div>
    <div class="mymiddle">
        <div class="myleft">
            
<? include template('menu','member',MOD_FLAG); ?>
        </div>
        <div class="myright">
            <div class="myright_top"></div>
            <div class="myright_middle">
                <h3>欢迎您回来，<?=$user->username?></h3>
                <div class="mainrail">
                    <table cellspacing="0" cellpadding="0" class="maintable">
                        <tr>
                            <td align="center" width="30%">
                                <a href="<?php echo url("member/index/ac/face"); ?>"><img src="<?php echo get_face($user->uid); ?>" style="padding:1px;border:1px solid #eee;" /></a><br />
                                <b><?=$user->username?></b>
                            </td>
                            <td valign="top" width="40%">
                                UID：<?=$user->uid?><br />
                                会员组：<?php echo template_print('member','group',array('groupid'=>"{$user->groupid}",));?>&nbsp;<? if($user->nexttime) { ?>
(到期:<?php echo newdate($user->nexttime,'Y-m-d'); ?>)<? } ?>
<br />
                                登陆&nbsp;IP：<?=$user->loginip?><br />
                                注册时间：<?php echo newdate($user->regdate, 'Y-m-d H:i'); ?><br />
                                电子邮件：<?=$user->email?><br />
                            </td>
                            <td valign="top" width="30%"> 
                                等级积分：<span class="font_2"><?=$user->point?></span><br />
                                金币：<span class="font_2"><?=$user->coin?></span><br />
                                点评：<span class="font_2"><?=$user->reviews?></span><br />
                                鲜花：<span class="font_2"><?=$user->flowers?></span><br />
                                登录次数：<span class="font_2"><?=$user->logincount?></span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="mybottom"></div>
</div><?php footer(); ?>