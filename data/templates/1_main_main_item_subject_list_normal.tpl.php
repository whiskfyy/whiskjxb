<? !defined('IN_MUDDER') && exit('Access Denied'); if($total) { ?>
<?php $index=0; if(is_array($resList)) { foreach($resList as $val) { ?>
    <div class="itemlist<? if($val['finer']) { ?>
 itemfiner<? } ?>
">
        <div class="pic">
            <div id="item_pic_<?=$val['sid']?>"><a href="<?php echo url("item/detail/id/{$val['sid']}"); ?>"><img<? if($val['thumb']) { ?>
 onmouseover="tip_start(this);" <? } ?>
 src="<?=URLROOT?>/<? if($val['thumb']) { ?>
<?=$val['thumb']?>
<? } else { ?>
static/images/noimg.gif<? } ?>
" /></a><b></b></div>
        </div>
        <div class="item">
            <div class="info">
                <div>
                    <h3 id="item_name_<?=$val['sid']?>"><a href="<?php echo url("item/detail/id/{$val['sid']}"); ?>"><?=$val['name']?>&nbsp;<?=$val['subname']?></a></h3>
                </div>
                <table class="custom_field_list" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
                            
<? if(is_array($reviewpot)) { foreach($reviewpot as $_val) { ?>
<?=$_val['name']?>:<span class="font_2" style="font-size:16px;"><?php echo cfloat($val[$_val['flag']]); ?></span>
<? } } ?>
&nbsp;综合得分:<span class="font_1" style="font-size:16px;"><strong><?php echo cfloat($val['avgsort']); ?></strong></span>
                        </td>
                    </tr>
                    
<? if(is_array($custom_field)) { foreach($custom_field as $_val) { ?>
                        <?php echo $FD->detail($_val, $val[$_val['fieldname']], $val['sid'])."\r\n"; ?>                    
<? } } ?>
                </table>
            </div>
            <div class="stat">
                <?php $reviewcfg = $_G['loader']->variable('config','review'); ?>                <li class="start<?php echo get_star($val['avgsort'],$reviewcfg['scoretype']);; ?>"></li>
                <? if($catcfg['useprice']) { ?>
                <li><span class="font_2"><?=$val['avgprice']?></span> <?=$catcfg['useprice_unit']?></li>
                <? } ?>
                <li><a href="<?php echo url("item/detail/id/{$val['sid']}"); ?>#review"><span class="font_2"><?=$val['reviews']?></span> 条点评</a></li>
                <li><a href="<?php echo url("item/pic/sid/{$val['sid']}"); ?>"><span class="font_2"><?=$val['pictures']?></span> 张图</a></li>
            </div>
        </div>
        <div class="clear"></div>
    </div><? if(++$index==2) { ?>
	<div id="adv_1"></div>
	<? } } } } else { ?>
    <div class="messageborder">
        <span class="msg-ico">本板块暂时没有主题信息，<a href="<?php echo url("item/member/ac/subject_add/pid/{$pid}"); ?>">我要添加</a>。</span>
    </div><? } ?>
