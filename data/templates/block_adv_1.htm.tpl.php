<? !defined('IN_MUDDER') && exit('Access Denied'); ?>
<div class="ix_foo">
<?php $_QUERY['get_ad']=$_G['datacall']->datacall_get('getlist',array('apid'=>1,'cachetime'=>1000,),'adv');
if(is_array($_QUERY['get_ad']))foreach($_QUERY['get_ad'] as $ad_k => $ad) { ?>
<div><?=$ad['code']?></div><?php }if(empty($_QUERY['get_ad'])){ ?>
<center>AD</center><?php } ?>
</div>