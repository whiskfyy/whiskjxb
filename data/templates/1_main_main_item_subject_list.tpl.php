<? !defined('IN_MUDDER') && exit('Access Denied'); ?><?php $_HEAD['title'] = $pcat[$pid]['name']. '大全' .
        ($catid != $pid ? ($_CFG['titlesplit'] . $pcat[$catid]['name']) : '' ) .
        ($boroughs[$aid] ? ($_CFG['titlesplit'] . $boroughs[$aid]) : '' ) .
        ($streets[$aid] ? ($_CFG['titlesplit'] . $streets[$aid]) : '' );
 include template('modoer_header'); ?>
<div id="body">
	<div id="list_left">
    <div class="catefoot">
        <div class="link_path">
            <em><a href="<?php echo url("review/list/type/item_subject/catid/{$catid}"); ?>">最新点评</a>&nbsp;-&nbsp;<a href="<?php echo url("item/allpic/catid/{$catid}"); ?>"><?=$pcat[$pid]['name']?>图片</a>&nbsp;-&nbsp;<a href="<?php echo url("item/top/catid/{$catid}"); ?>">最佳排行</a>&nbsp;-&nbsp;<a href="<?php echo url("item/detail/random/1"); ?>">随便看看</a></em>
            <a href="<?php echo url("modoer/index"); ?>">首页</a>&nbsp;&raquo;&nbsp;<?php echo implode('&nbsp;&raquo;&nbsp;', $urlpath); ?><span class="font_3">(<?=$total?>)</span>
        </div>
        <? if($category_level<=2 && $subcats) { ?>
        <ul class="cate">
            <h2>按分类查找:</h2>
            
<?php $_QUERY['get_val']=$_G['datacall']->datacall_get('category',array('pid'=>"{$catid}",),'item');
if(is_array($_QUERY['get_val']))foreach($_QUERY['get_val'] as $val_k => $val) { ?>
            <li><span<? if($val['catid']==$catid) { ?>
 class="selected"<? } ?>
><a href="<?php echo url("item/list/catid/{$val['catid']}/aid/{$aid}"); ?>"><?=$val['name']?></a></span></li>
            <?php } ?>
        </ul>
<? } else { ?>
        <ul class="cate">
            <h2>按分类查找:</h2>
            
<?php $_QUERY['get_val']=$_G['datacall']->datacall_get('category',array('pid'=>"{$category[$catid]['pid']}",),'item');
if(is_array($_QUERY['get_val']))foreach($_QUERY['get_val'] as $val_k => $val) { ?>
            <li><span<? if($val['catid']==$catid) { ?>
 class="selected"<? } ?>
><a href="<?php echo url("item/list/catid/{$val['catid']}/aid/{$aid}"); ?>"><?=$val['name']?></a></span></li>
            <?php } ?>
        </ul>
        <? } ?>
        <div class="clear"></div>
        <? if($model['usearea']) { ?>
        <ul class="cate">
            <h2>按地区查找:</h2>
            <li><span<? if(!$aid) { ?>
 class="selected"<? } ?>
><a href="<?php echo url("item/list/catid/{$catid}/"); ?>">全部地区</a></span></li>
            
<? if(is_array($boroughs)) { foreach($boroughs as $key => $val) { ?>
            <li><span<? if($aid==$key||$paid==$key) { ?>
 class="selected"<? } ?>
><a href="<?php echo url("item/list/catid/{$catid}/aid/{$key}"); ?>"><?=$val?></a></span></li>
            
<? } } ?>
        </ul>
        <div class="clear"></div>
        <? if($streets) { ?>
        <ul class="cate">
            <h2>按街道查找：</h2>
            
<? if(is_array($streets)) { foreach($streets as $key => $val) { ?>
                <li><span<? if($aid==$key) { ?>
 class="selected"<? } ?>
><a href="<?php echo url("item/list/catid/{$catid}/aid/{$key}"); ?>"><?=$val?></a></span></li>
            
<? } } ?>
        </ul>
        <? } ?>
        <? } ?>
        <div class="clear"></div>
        <? if($catcfg['attcat']) { ?>
        <?php $att_cats = $_G['loader']->variable('att_cat','item'); ?>        
<? if(is_array($catcfg['attcat'])) { foreach($catcfg['attcat'] as $att_catid) { ?>
        <? if($att_cats[$att_catid]) { ?>
        <ul class="cate">
            <h2>按<?=$att_cats[$att_catid]['name']?>查找:</h2>
            <?php $att_url = item_att_url($att_catid,0,1); ?>            <li><span<? if(!$atts[$att_catid]) { ?>
 class="selected"<? } ?>
><a href="<?php echo url("item/list/catid/{$catid}/att/{$att_url}"); ?>">全部</a></span></li>
            
<?php $_QUERY['get_val']=$_G['datacall']->datacall_get('attlist',array('catid'=>"{$att_catid}",),'item');
if(is_array($_QUERY['get_val']))foreach($_QUERY['get_val'] as $val_k => $val) { ?>
            <?php $att_url = item_att_url($att_catid,$val_k); ?>            <li><span<? if($atts[$att_catid]==$val_k) { ?>
 class="selected"<? } ?>
><a href="<?php echo url("item/list/catid/{$catid}/aid/{$aid}/att/{$att_url}"); ?>"><?=$val?></a></span></li>
            <?php } ?>
        </ul>
        <div class="clear"></div>
        <? } ?>
        
<? } } ?>
        <? } ?>
    </div>

    <div class="subrail">
        显示: 
<? if(is_array($typelist)) { foreach($typelist as $key => $val) { ?>
        <span<?=$active['type'][$key]?>><a href="javascript:;" onclick="list_display('item_subject_type','<?=$key?>')"><?=$val?></a></span>
        
<? } } ?>
&nbsp;|&nbsp;
        数量: 
<? if(is_array($numlist)) { foreach($numlist as $val) { ?>
        <span<?=$active['num'][$val]?>><a href="javascript:;" onclick="list_display('item_subject_num','<?=$val?>')"><?=$val?></a></span>
        
<? } } ?>
&nbsp;|&nbsp;
        排序:
<? if(is_array($orderlist)) { foreach($orderlist as $key => $val) { ?>
        <span<?=$active['orderby'][$key]?>><a href="javascript:;" onclick="list_display('item_subject_orderby','<?=$key?>')"><?=$val?></a></span>
        
<? } } ?>
    </div>

    <div class="mainrail">
        <? if($type == 'pic') { ?>
        
<? include template('item_subject_list_pic'); ?>
        
<? } else { ?>
        
<? include template('item_subject_list_normal'); ?>
        <? } ?>
        </div>
        <? if($multipage) { ?>
<div class="multipage"><?=$multipage?></div><? } ?>
    </div>

    <div id="list_right">

        <div class="mainrail">
            <em>
                <span class="selected" id="btn_subject1" onclick="tabSelect(1,'subject')">热门</span>
                <? if($catcfg['useeffect']) { ?>
                <? if($catcfg['effect1']) { ?>
<span class="unselected" id="btn_subject2" onclick="tabSelect(2,'subject')"><?=$catcfg['effect1']?></span><? } ?>
                <? if($catcfg['effect1']) { ?>
<span class="unselected" id="btn_subject3" onclick="tabSelect(3,'subject')"><?=$catcfg['effect2']?></span><? } ?>
                <? } ?>
            </em>
            <h3 class="rail-h-1 rail-h-bg-5">热门<?=$model['item_name']?></h3>
            <div id="subject1">
                <?php $reviewcfg = $_G['loader']->variable('config','review'); ?>                <ul class="rail-pic">
                    
<?php $_QUERY['get_val']=$_G['datacall']->datacall_get('table',array('table'=>"dbpre_subject",'select'=>"sid,pid,catid,name,subname,reviews,avgsort,pageviews,thumb",'where'=>"pid='{$pid}' AND pageviews>0",'orderby'=>"pageviews DESC",'rows'=>5,),'');
if(is_array($_QUERY['get_val']))foreach($_QUERY['get_val'] as $val_k => $val) { ?>
                    <?php if(isset($val['name'])) $val_name = substr($val['name'],0,10).'********';
						 ?>                    <li>
                        <div class="pic">
                            <a href="<?php echo url("item/detail/id/{$val['sid']}"); ?>"><img src="<?=URLROOT?>/<? if($val['thumb']) { ?>
<?=$val['thumb']?>
<? } else { ?>
static/images/s_noimg.gif<? } ?>
" alt="<?=$val_name?> <?=$val['subname']?>" /></a>
                        </div>
                        <div class="info">
                            <a href="<?php echo url("item/detail/id/{$val['sid']}"); ?>" title="<?=$val_name?> <?=$val['subname']?>"><?php echo trimmed_title($val_name.$val['subname'],18); ?></a><br />
                            <span class="font_2"><b><?=$val['pageviews']?></b></span>浏览,<span class="font_2"><b><?=$val['reviews']?></b></span>点评
                            <p class="start<?php echo get_star($val['avgsort'], $reviewcfg['scoretype']); ?>" style="margin:0;padding:0;height:15px;"></p>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <? if($catcfg['useeffect']) { ?>
            <div class="none" id="subject2" datacallname="主题_会员参与" params="{'pid':'<?=$pid?>','idtype':'<?=$model['tablename']?>','effect':'effect1'}"></div>
            <div class="none" id="subject3" datacallname="主题_会员参与" params="{'pid':'<?=$pid?>','idtype':'<?=$model['tablename']?>','effect':'effect2'}"></div>
            <? } ?>
        </div>
        <div class="mainrail">
            <h3 class="rail-h-1 rail-h-bg-5">热门分类</h3>
            <div id="hot_category">
                <ul class="rail-list">
                    
<?php $_QUERY['get_val']=$_G['datacall']->datacall_get('table',array('table'=>"dbpre_category",'select'=>"catid,pid,name,total",'where'=>"pid={$pid} AND total>0",'orderby'=>"total DESC",'rows'=>10,),'');
if(is_array($_QUERY['get_val']))foreach($_QUERY['get_val'] as $val_k => $val) { ?>
                    <li><cite><?=$val['total']?>&nbsp;个</cite><a href="<?php echo url("item/list/catid/{$val['catid']}"); ?>"><?=$val['name']?></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>

        <? if($cookie_subjects) { ?>
        <div class="mainrail" id="cookieitems">
            <em><a href="javascript:clear_cookie_subject(<?=$pid?>);">清除记录</a></em>
            <h3 class="rail-h-1 rail-h-bg-5">历史浏览</h3>
            <ul class="rail-list">
                
<? if(is_array($cookie_subjects)) { foreach($cookie_subjects as $key => $val) { ?>
                <li><a href="<?php echo url("item/detail/id/{$key}"); ?>" title="<?=$val?>"><?=$val?></a></li>
                
<? } } ?>
            </ul>
        </div>
        <? } ?>
</div>

    <div class="clear"></div>

</div>
<div id="adv_1_content" style="display:none;">
<? if($_incfile_=display('adv:show','name/主题列表页_列表间广告'))include_once($_incfile_);?>
</div>
<script type="text/javascript">
//加载广告
replace_content('adv_1=adv_1_content');
</script><?php footer(); ?>