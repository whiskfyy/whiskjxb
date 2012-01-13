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
                <h3>我的收藏夹</h3>
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
                            <th width="25">删?</th>
                            <th width="*"><?=$title?> <?=$subtitle?></th>
                            <th width="120">收藏时间</th>
                        </tr>
                        <? if($total) { ?>
                        
<? if($list) { while($val = $list->fetch_array()) { ?>
                        <?php if(isset($val['name'])) $val_name = substr($val['name'],0,10).'********';
						 ?>                        <tr>
                            <td><input type="checkbox" name="fids[]" value="<?=$val['fid']?>" /></td>
                            <td><a href="<?php echo url("item/detail/id/{$val['sid']}"); ?>" target="_blank"><?=$val_name?> <?=$val['subname']?></a></td>
                            <td><?php echo newdate($val['addtime'],'Y-m-d H:i'); ?></td>
                        </tr>
                        
<? } } ?>
                        
<? } else { ?>
                        <tr><td colspan="3">暂无信息。</td></tr>
                        <? } ?>
                    </table>
                    <? if($total) { ?>
                    <div class="multipage"><?=$multipage?></div>
                    <div class="text_center">
                        <input type="hidden" name="dosubmit" value="yes" />
                        <input type="hidden" name="op" value="delete" />
                        <button type="button" onclick="easy_submit('myform','delete','fids[]');">删除所选</button>
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