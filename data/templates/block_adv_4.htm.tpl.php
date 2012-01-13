<? !defined('IN_MUDDER') && exit('Access Denied'); ?>
<div style="padding-bottom:5px;border-bottom:1px dashed #ddd;margin:5px 0;">
<?php $_QUERY['get_ad']=$_G['datacall']->datacall_get('getlist',array('apid'=>4,'cachetime'=>1000,),'adv');
if(is_array($_QUERY['get_ad']))foreach($_QUERY['get_ad'] as $ad_k => $ad) { ?>
<div style="text-align:center;"><?=$ad['code']?></div><?php }if(empty($_QUERY['get_ad'])){ ?>
<center>AD</center><?php } ?>
</div>