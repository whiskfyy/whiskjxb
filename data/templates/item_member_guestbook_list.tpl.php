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
                    <ul class="optabs">
<? if(is_array($category)) { foreach($category as $val) { if(!$val['pid']) { ?>
<li<? if($val['catid']==$pid) { ?>
 class="active"<? } ?>
><a href="<?php echo url("item/member/ac/{$ac}/pid/{$val['catid']}"); ?>"><?=$val['name']?></a></li><? } } } ?>
</ul><div class="clear"></div>
                    <form method="post" name="myform" action="<?php echo url("item/member/ac/{$ac}/rand/{$_G['random']}"); ?>">
                    <table width="100%" cellspacing="0" cellpadding="0" class="maintable" trmouse="Y">
                        <tr>
                            <th width="20" align="center"><input type="checkbox" onclick="checkbox_checked('guestbookids[]',this);" /></th>
                            <th width="120"><?=$title?> <?=$subtitle?></th>
                            <th width="*">内容</th>
                            <th width="50">状态</th>
                            <th width="110">发布/回复时间</th>
                            <? if($ac == 'g_guestbook') { ?>
                            <th width="50">编辑</th>
                            <? } ?>
                        </tr>
                        <? if($total) { ?>
                        
<? if($list) { while($val = $list->fetch_array()) { ?>
                        <tr>
                            <td><input type="checkbox" name="guestbookids[]" value="<?=$val['guestbookid']?>" /></td>
                            <td><a href="<?php echo url("item/detail/id/{$val['sid']}"); ?>" target="_blank"><?=$val['name']?>&nbsp;<?=$val['subname']?></a></td>
                            <td>
                                <div><?=$val['content']?></div>
                                <? if($val['reply']) { ?>
<div class="font_2">回复:<?=$val['reply']?></div><? } ?>
                            </td>
                            <td><? if($val['status']) { ?>
已审核
<? } else { ?>
未审核<? } ?>
</td>
                            <td>
                                <div><?php echo newdate($val['dateline'],'Y-m-d H:i'); ?></div>
                                <? if($val['reply']) { ?>
<div class="font_2"><?php echo newdate($val['replytime'],'Y-m-d H:i'); ?></div><? } ?>
                            </td>
                            <? if($ac == 'g_guestbook') { ?>
                            <td><a href="<?php echo url("item/member/ac/g_guestbook/op/reply/guestbookid/{$val['guestbookid']}"); ?>">回复</a></td>
                            <? } ?>
                        </tr>
                        
<? } } ?>
                        
<? } else { ?>
                        <tr><td colspan="7">暂无信息。</td></tr>
                        <? } ?>
                    </table>
                    <? if($total) { ?>
                    <div class="multipage"><?=$multipage?></div>
                    <div class="text_center">
                        <input type="hidden" name="dosubmit" value="yes" />
                        <input type="hidden" name="op" value="delete" />
                        <button type="button" onclick="easy_submit('myform','delete','guestbookids[]');">删除所选</button>
                    </div>
                    <? } ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="mybottom"></div>
</div><?php footer(); ?>