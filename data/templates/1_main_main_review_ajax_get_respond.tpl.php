<? !defined('IN_MUDDER') && exit('Access Denied'); if($total) { ?>
<div class="resplods_list" id="resplod">
<h3><em>[<a href="javascript:post_respond(<?=$rid?>);">发表回应</a>]</em>回应 ( 共 <?=$total?> 条 )</h3>
<ul id="resplod_ul">
<? if($list) { while($val = $list->fetch_array()) { ?>
    <li id="respond_<?=$val['respondid']?>_li">
        <div class="face"><a href="<?php echo url("space/index/uid/{$val['uid']}"); ?>" target="_blank"><img src="<?php echo get_face($val['uid']); ?>"></a></div>
        <div class="content">
            <div class="title">
                <? if($user->uid == $val['uid']) { ?>
                <em><a href="javascript:delete_respond(<?=$val['respondid']?>,<?=$val['rid']?>);">删除</a></em>
                <? } ?>
                <a href="<?php echo url("space/index/uid/{$val['uid']}"); ?>" target="_blank"><?=$val['username']?></a>&nbsp;<span class="font_3"><?php echo newdate($val['posttime'], 'w2style'); ?>&nbsp;回复</span>
            </div>
            <div class="detail" id="respond_<?=$val['respondid']?>"><?=$val['content']?></div>
        </div>
        <div class="clear"></div>
    </li>
<? } } ?>
</ul>
<div class="page"><?=$multipage?></div>
<div class="clear"></div>
<? } else { ?>
暂无信息。<? } ?>
