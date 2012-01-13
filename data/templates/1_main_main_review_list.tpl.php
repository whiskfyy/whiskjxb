<? !defined('IN_MUDDER') && exit('Access Denied'); ?><?php $_HEAD['title'] = $day_arr_lng[$day] . $filter_arr_lng[$filter] . '点评列表';
 include template('modoer_header'); ?>
<div id="body">
    <div class="link_path">
        <em>共找到 <span class="font_2"><?=$total?></span> 个点评</em>
        <a href="<?php echo url("modoer/index"); ?>">
<? echo lang('global_index'); ?>
</a>&raquo;&nbsp;<?php echo implode('&nbsp;&raquo;&nbsp;', $urlpath); ?></span>
    </div>

    <div class="review_left">
		<div class="g-list-category">
			<div class="g-list-category-type">
                <? if($category) { ?>
				<h3>分类:</h3>
				<ul class="g-list-category-class">
<? if(is_array($category)) { foreach($category as $key => $val) { ?>
<li<?=$active['category'][$key]?>><a href="<?php echo url("review/list/type/{$type}/pid/{$key}/day/{$day}/filter/{$filter}"); ?>"><?=$val?></a></li>
<? } } ?>
</ul>
				<div class="clear"></div>
                <? } ?>
<h3>范围:</h3>
				<ul class="g-list-category-class">
<? if(is_array($day_arr_lng)) { foreach($day_arr_lng as $key => $val) { ?>
<li<?=$active['day'][$key]?>><a href="<?php echo url("review/list/type/{$type}/pid/{$pid}/day/{$key}/filter/{$filter}"); ?>"><?=$val?></a></li>
<? } } ?>
</ul>
				<div class="clear"></div>
				<h3>筛选:</h3>
				<ul class="g-list-category-class">
<? if(is_array($filter_arr_lng)) { foreach($filter_arr_lng as $key => $val) { ?>
<li<?=$active['filter'][$key]?>><a href="<?php echo url("review/list/type/{$type}/pid/{$pid}/day/{$day}/filter/{$key}"); ?>"><?=$val?></a></li>
<? } } ?>
</ul>
				<div class="clear"></div>
			</div>
		</div>
		<div class="subrail">
			排序方式:
            
<? if(is_array($orderby_arr_lng)) { foreach($orderby_arr_lng as $key => $val) { ?>
            <span<?=$active['orderby'][$key]?>><a href="javascript:;" onclick="list_display('review_list_orderby','<?=$key?>')"><?=$val?></a></span>
            
<? } } ?>
</div>

        <div class="mainrail review-view">
            
<? if(is_array($resList)) { foreach($resList as $val) { ?>
            <div class="review">
                <div class="member m_w_item">
                    <a href="<?php echo url("space/index/uid/{$val['uid']}"); ?>"><img src="<?php echo get_face($val['uid']); ?>" class="face"></a>
                    <ul>
                        <? if($val['uid']) { ?>
                        <li><?php echo template_print('member','group',array('groupid'=>"{$val['groupid']}",));?></li>
                        <li>积分:<span><?=$val['point']?></span></li>
                        
<? } else { ?>
                        <li>游客</li>
                        <? } ?>
                    </ul>
                </div>
                <div class="field f_w_item">

                    <div class="feed">

                    <? if($val['uid']) { ?>
                        <em><a href="javascript:send_message(<?=$val['uid']?>);">发短信</a>, <a href="javascript:add_friend(<?=$val['uid']?>);">加好友</a></em>
                        <span class="member-ico"><a href="<?php echo url("space/index/uid/{$val['uid']}"); ?>"><?=$val['username']?></a></span>&nbsp;
                        
<? } else { ?>
                        <span class="font_3">游客(<?php echo preg_replace("/^([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)$/","\\1.\\2.*.*", $val['ip']); ?>)</span>
                        <? } ?>
                        在&nbsp;<?php echo newdate($val['posttime'], 'w2style'); ?>&nbsp;点评了&nbsp;<strong><a href="<?php echo template_print('review','typeurl',array('idtype'=>"{$val['idtype']}",'id'=>"{$val['id']}",));?>"><?=$val['subject']?></a></strong>
                    </div>

                    <div class="info<? if($val['digest']) { ?>
 review-digest<? } ?>
">
                        <ul class="score">
                            
<?php $_QUERY['get__val']=$_G['datacall']->datacall_get('reviewopt',array('catid'=>"{$val['pcatid']}",),'item');
if(is_array($_QUERY['get__val']))foreach($_QUERY['get__val'] as $_val_k => $_val) { ?>
                            <li><?=$_val['name']?></li><li class="start<?php echo cfloat($val[$_val['flag']]); ?>"></li>
                            <?php } ?>
                        </ul>
                        <div class="clear"></div>
                        <? if($val['title']!=$val['subject']) { ?>
                        <h4 class="title"><a href="<?php echo url("review/detail/id/{$val['rid']}"); ?>"><?=$val['title']?></a></h4>
                        <? } ?>
                        <? if($val['pictures']) { ?>
                        <div class="pictures">
                            <?php $val['pictures'] = unserialize($val['pictures']); ?>                            
<? if(is_array($val['pictures'])) { foreach($val['pictures'] as $pic) { ?>
                            <a href="<?=URLROOT?>/<?=$pic['picture']?>"><img src="<?=URLROOT?>/<?=$pic['thumb']?>" onmouseover="tip_start(this);" /></a>
                            
<? } } ?>
                        </div>
                        <? } ?>
                        <div style="min-height:50px;">
                            <p><?php echo nl2br($val['content']); ?></p>
                            <ul class="params">
                                <? if($val['price'] && $catcfg['useprice']) { ?>
                                <li><?=$catcfg['useprice_title']?>: <span class="font_2"><?=$val['price']?><?=$catcfg['useprice_unit']?></span></li>
                                <? } ?>
                                <?php $detail_tags = $val['taggroup'] ? @unserialize($val['taggroup']) : array(); ?>                                
<? if(is_array($taggroups)) { foreach($taggroups as $_key => $_val) { ?>
                                    <? if($detail_tags[$_key]) { ?>
                                        <li><?=$_val['name']?>:
                                        
<? if(is_array($detail_tags[$_key])) { foreach($detail_tags[$_key] as $tagid => $tagname) { ?>
                                            <a href="<?php echo url("item/tag/tagname/{$tagname}"); ?>"><?=$tagname?></a>
                                        
<? } } ?>
                                        </li>
                                    <? } ?>
                                
<? } } ?>
                            </ul>
                        </div>
                        <div id="flowers_<?=$val['rid']?>"></div>
                        <div id="responds_<?=$val['rid']?>"></div>
                        <div class="operation">
                            <span class="respond-ico"><a href="javascript:get_respond('<?=$val['rid']?>');">回应</a></span>(<span class="font_2" id="respond_<?=$val['rid']?>"><?=$val['responds']?></span> 条)&nbsp;
                            <span class="flower-ico"><a href="javascript:add_flower(<?=$val['rid']?>, <?=$val['pcatid']?>);">鲜花</a>(<a href="javascript:get_flower(<?=$val['rid']?>, <?=$val['pcatid']?>);"><span id="flower_<?=$val['rid']?>" class="font_2"><?=$val['flowers']?></span> 朵</a>)</span>&nbsp;
                            <a href="javascript:post_report(<?=$val['rid']?>, <?=$val['pcatid']?>);">举报</a>&nbsp;
                        </div>
                    </div>

                </div>
                <div class="clear"></div>
            </div>
            
<? } } ?>
            <? if($multipage) { ?>
<div class="multipage"><?=$multipage?></div><? } ?>
            <? if(!$total) { ?>
            <div class="messageborder">近期没有会员进行点评</a>。</div>
            <? } ?>
        </div>
    </div>

    <div class="review_right">
		<div class="mainrail rail-border-3">
			<h3 class="rail-h-3 rail-h-bg-3">搜索</h3>
			<div class="review-side-search">
				<form method="get" action="<?=URLROOT?>/index.php">
					<input type="hidden" name="m" value="review" />
					<input type="hidden" name="act" value="review" />
					<input type="text" name="keyword" class="t_input" value="<?=$keyword?>" />&nbsp;
					<button type="submit" class="button">搜索</button>
				</form>
			</div>
		</div>
        <? if($uid > 0) { ?>
        
<? include template('space_side'); ?>
        
<? } else { ?>
        <div class="mainrail rail-border-3">
            <h3 class="rail-h-3 rail-h-bg-3">点评专家</h3>
            <ul class="rail-list">
                
<?php $_QUERY['get_val']=$_G['datacall']->datacall_get('table',array('table'=>"dbpre_members",'select'=>"uid,username,reviews",'where'=>"reviews>0",'orderby'=>"reviews DESC",'rows'=>10,'cachetime'=>3600,),'');
if(is_array($_QUERY['get_val']))foreach($_QUERY['get_val'] as $val_k => $val) { ?>
                <li><img src="<?php echo get_face($val['uid']);; ?>" width="20" height="20" /><cite><?=$val['reviews']?>&nbsp;点评</cite><a href="<?php echo url("space/index/uid/{$val['uid']}"); ?>"><?=$val['username']?></a></li>
                <?php } ?>
            </ul>
            <div class="clear"></div>
        </div>
        <div class="mainrail rail-border-3">
            <h3 class="rail-h-3 rail-h-bg-3">本月活跃会员</h3>
            <?php $begintime = date('Ymd', $_G['timestamp'] - 30 * 24 * 3600);
                $endtime = date('Ymd', $_G['timestamp']);
             ?>            <ul class="rail-faces">
                
<?php $_QUERY['get_val']=$_G['datacall']->datacall_get('sql',array('sql'=>"SELECT uid,username,SUM(reviews) as reviews FROM dbpre_activity WHERE dateline BETWEEN {$begintime} AND {$endtime} GROUP BY username ORDER BY reviews DESC,subjects DESC",'rows'=>9,'cachetime'=>3600,),'');
if(is_array($_QUERY['get_val']))foreach($_QUERY['get_val'] as $val_k => $val) { ?>
                <li><div><a href="<?php echo url("space/index/uid/{$val['uid']}"); ?>"><img src="<?php echo get_face($val['uid']); ?>" /></a><a href="<?php echo url("space/index/uid/{$val['uid']}"); ?>" title="<?=$val['username']?>"></div><span><?=$val['username']?></span></a></li>
                <?php } ?>
            </ul>
            <div class="clear"></div>
        </div>
        <? } ?>
    </div>

    <div class="clear"></div>

</div><?php footer(); ?>