<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<?if(DEBUG==FALSE && $url_forward) {?>
<script type="text/javascript">
window.onload = function() {
    setTimeout(do_location, 2000);
}

function do_location() {
    document.location.href = '<?=$url_forward?>';
}
</script>
<? } ?>
<script type="text/javascript" src="./static/javascript/jquery.js"></script>
<div id="body">
    <center>
    <table cellspacing="0" cellpadding="0" align="center" style="border: 1px solid #CCCCCC;background:#FFF;width:600px;height:150px;margin:50px 0;">
        <tr><td align="center"><center style="font-size:14px;"><?=$message?><? if($url_forward != null) { ?><br /><br /><a href="<?=$url_forward?>"><?=lang('global_message_des')?></a><? } ?></center></td></tr>
    </table>
    </center>
</div>