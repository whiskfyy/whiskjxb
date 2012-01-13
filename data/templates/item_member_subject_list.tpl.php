<? !defined('IN_MUDDER') && exit('Access Denied'); include template('modoer_header'); ?>
<div id="body">
    <div class="myhead"></div>
    <div class="mymiddle">
        <div class="myleft">
            
<? include template('menu','member','member'); ?>
        </div>
        <div class="myright">
            <div class="myright_top"></div>
            <div class="myright_middle">
                <h3><?=$path_title?></h3>
                <div class="mainrail">
                    <form method="post" name="myform" action="<?php echo url("item/member/ac/{$ac}/rand/{$_G['random']}"); ?>">
                    <ul class="optabs">
<? if(is_array($category)) { foreach($category as $val) { if(!$val['pid']) { ?>
<li<? if($val['catid']==$pid) { ?>
 class="active"<? } ?>
><a href="<?php echo url("item/member/ac/{$ac}/pid/{$val['catid']}"); ?>"><?=$val['name']?></a></li><? } } } ?>
<li class="act"><a href="<?php echo url("item/member/ac/subject_add/pid/{$pid}"); ?>">增加新<?=$model['item_name']?></a></li></ul><div class="clear"></div>
                    <table width="100%" cellspacing="0" cellpadding="0" class="maintable" trmouse="Y">
                        <tr>
                            <th width="*"><?=$title?> <?=$subtitle?></th>
                            <th width="85"><?=$cattitle?></th>
                            <th width="35"><center>点评</center></th>
                            <th width="35"><center>留言</center></th>
                            <th width="35"><center>图片</center></th>
                            <th width="115">登记时间</th>
                            <? if($ac=='g_subject') { ?>
                            <th width="100">管理有效期至</th>
                            <? } ?>
                            <th width="60">状态</th>
                        </tr>
                        <? if($total) { ?>
                        
<? if($list) { while($val = $list->fetch_array()) { ?>
                        <tr>
                            <td>
                                <div><a href="<?php echo url("item/detail/id/{$val['sid']}"); ?>" target="_blank"><?=$val['name'].$val['subname']?></a><div>
                                <div>
                                    操作：<? if($ac=='g_subject') { ?>
                                    <a href="<?php echo url("item/member/ac/subject_edit/sid/{$val['sid']}"); ?>">编辑</a>
                                    <a href="<?php echo url("item/member/ac/subject_edit/op/delete/sid/{$val['sid']}"); ?>" onclick="return confirm('您确定要进行删除操作吗？');">删除</a>
                                    <? } ?>
                                    <a href="<?php echo url("item/member/ac/review_add/sid/{$val['sid']}"); ?>">点评</a>
                                    <a href="<?php echo url("item/member/ac/pic_upload/sid/{$val['sid']}"); ?>">传图</a>
                                </div>
                            </td>
                            <td><a href="<?php echo url("item/list/catid/{$val['catid']}"); ?>" target="_blank"><?php echo template_print('item','category',array('catid'=>"{$val['catid']}",));?></a></td>
                            <td><center><?=$val['reviews']?></center></td>
                            <td><center><?=$val['guestbooks']?></center></td>
                            <td><center><?=$val['pictures']?></center></td>
                            <td><?php echo newdate($val['addtime'],'Y-m-d H:i'); ?></td>
                            <? if($ac=='g_subject') { ?>
                            <td><? if($val['expirydate']) { ?>
<?php echo newdate($val['expirydate'],'Y-m-d'); } else { ?>
无期限<? } ?>
</td>
                            <? } ?>
                            <td><? if($val['status']) { ?>
已审核
<? } else { ?>
<span class="font_1">未审核</span><? } ?>
</td>
                        </tr>
                        
<? } } ?>
                        
<? } else { ?>
                        <tr>
                            <td colspan="7">暂无信息。</td>
                        </tr>
                        <? } ?>
                    </table>
                    </form>
                    <div class="multipage"><?=$multipage?></div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="mybottom"></div>
</div><?php footer(); ?>