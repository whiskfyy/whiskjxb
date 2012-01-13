<? !defined('IN_MUDDER') && exit('Access Denied'); ?><?php $_HEAD['title'] = '主题分类';
 include template('modoer_header'); ?>
<div id="body">
	<div id="class_left">
		<div class="mainrail">
<?php $_QUERY['get_val']=$_G['datacall']->datacall_get('category',array('pid'=>0,),'item');
if(is_array($_QUERY['get_val']))foreach($_QUERY['get_val'] as $val_k => $val) { ?>
            <div class="mainrail rail-border-3">
                <h3 class="rail-h-3 rail-h-bg-3"><b><a href="<?php echo url("item/list/catid/{$val['catid']}"); ?>"><?=$val['name']?></a></b></h3>
                <ul class="class-ul">
                    
<?php $_QUERY['get_val2']=$_G['datacall']->datacall_get('category',array('pid'=>"{$val['catid']}",),'item');
if(is_array($_QUERY['get_val2']))foreach($_QUERY['get_val2'] as $val2_k => $val2) { ?>
                    <li><a href="<?php echo url("item/list/catid/{$val2['catid']}"); ?>"><?=$val2['name']?></a></li>
                    <?php } ?>
                </ul>
                <div class="class-finer">
                    <ul class="class-finer-ul">
                        
<?php $_QUERY['get_val3']=$_G['datacall']->datacall_get('subject',array('select'=>"sid,name,subname,reviews,thumb,best,description",'pid'=>"{$val['catid']}",'rand'=>1,'rows'=>2,'cachetime'=>500,),'item');
if(is_array($_QUERY['get_val3']))foreach($_QUERY['get_val3'] as $val3_k => $val3) { ?>
                        <?php if(isset($val3['name'])) $val3_name = substr($val3['name'],0,10).'********';
						 ?>                        <li class="class-finer-subject">
                            <div class="class-finer-subject-img">
                                <a href="<?php echo url("item/detail/id/{$val3['sid']}"); ?>" title="<?=$val3_name?>"><img src="<?=URLROOT?>/<? if($val3['thumb']) { ?>
<?=$val3['thumb']?>
<? } else { ?>
static/images/noimg.gif<? } ?>
" /></a>
                             </div>
                            <div class="class-finer-subject-info">
                                <? if($val3['reviews']) { ?>
<em>好评率<span class="font_1"><?php echo round($val3['best']/$val3['reviews']*100); ?>%</span></em><? } ?>
                                <h3><a href="<?php echo url("item/detail/id/{$val3['sid']}"); ?>"><?php echo trimmed_title($val3_name.$val3['subname'],8,'...'); ?></a></h3>
                                <p><?php echo trimmed_title($val3['description'],33,'...'); ?><br /><a href="<?php echo url("item/detail/id/{$val3['sid']}"); ?>#review">共<?=$val3['reviews']?>条点评</a></p>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <?php } ?>
                    </ul>
                    <div class="clear"></div>
                </div>
            </div><?php } ?>
</div>
	</div>

    <div id="class_right">
        <div class="mainrail rail-border-3">
            <h3 class="rail-h-3 rail-h-bg-3">最新点评</h3>
            <ul class="rail-list">
<?php $_QUERY['get_val']=$_G['datacall']->datacall_get('review',array('orderby'=>"posttime DESC",'rows'=>8,'cachetime'=>500,),'review');
if(is_array($_QUERY['get_val']))foreach($_QUERY['get_val'] as $val_k => $val) { ?>
<?php if(isset($val['subject'])) $val_subject = substr($val['subject'],0,10).'********';
						 ?><li style="border-bottom:1px dashed #ddd;">
					<div class="category-review-title"><? if($val['uid']) { ?>
							<span class="member-ico"><a href="<?php echo url("space/index/uid/{$val['uid']}"); ?>"><?php echo trimmed_title($val['username'],6); ?></a></span>
<? } else { ?>
<span class="font_3">游客(<?php echo preg_replace("/^([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)$/","\\1.\\2.*.*", $val['ip']); ?>)</span>
					<? } ?>
<span class="font_2"><? if($val['best']) { ?>
好评
<? } else { ?>
差评<? } ?>
</span>&nbsp;<a href="<?php echo url("item/detail/id/{$val['id']}"); ?>"><?=$val_subject?></a>
					</div>
					<div class="category-review-info">
                        <? if($val['title']) { ?>
<h3><a href="<?php echo url("review/detail/id/{$val['rid']}"); ?>"><?php echo trimmed_title($val['title'], 20); ?></a></h3><? } ?>
<?php echo trimmed_title($val['content'],35,'...'); ?></div>
				</li><?php } ?>
            </ul>
        </div>
	</div>
    <div class="clear"></div>
</div><?php footer(); ?>