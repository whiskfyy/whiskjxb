<? !defined('IN_MUDDER') && exit('Access Denied'); ?><?php $_HEAD['title'] = $detail['name'] . $detail['subname'];
 include template('modoer_header'); ?>
<script type="text/javascript">
loadscript('swfobject');
$(document).ready(function() {
    if(!$.browser.msie && !$.browser.safari) {
        var d = $("#thumb");
        var dh = parseInt(d.css("height").replace("px",''));
        var ih = parseInt(d.find("img").css("height").replace("px",''));
        if(dh - ih < 3) return;
        var top = Math.ceil((dh - ih) / 2);
        d.find("img").css("margin-top",top+"px");
    }

    <? if($MOD['show_thumb'] && $detail['pictures']) { ?>
    get_pictures(<?=$sid?>);
    <? } ?>
    get_membereffect(<?=$sid?>, <?=$modelid?>);
});
</script>

<div id="body">

<div class="link_path">
    <em>已浏览：<span class="font_2"><?=$detail['pageviews']?></span></em>
    <a href="<?php echo url("modoer/index"); ?>">首页</a>&nbsp;&raquo;&nbsp;<?php echo implode('&nbsp;&raquo;&nbsp;', $urlpath); ?>&nbsp;&raquo;&nbsp;<?=$detail['name']?>&nbsp;<?=$detail['subname']?>
</div>

<div id="item_left">
    <div class="mainrail rail-border-3 item">
        <div class="rail-h-bg-shop header">
            <em>
                <?php $reviewcfg = $_G['loader']->variable('config','review'); ?>                <p class="start start<?php echo get_star($detail['avgsort'],$reviewcfg['scoretype']); ?>"></p>
                <? if($detail['owner']) { ?>
                &nbsp;管理员: <span class="member-ico"><a href="<?php echo url("space/index/username/{$detail['owner']}"); ?>" target="_blank"><?=$detail['owner']?></a></span>
                
<? } elseif($catcfg['subject_apply']) { ?>
                <span class="member-ico"><a href="<?php echo url("item/member/ac/subject_apply/sid/{$detail['sid']}"); ?>">认领<?=$model['item_name']?></a></span>
                <? } ?>
                <? if($catcfg['use_subbranch']) { ?>
                &nbsp;<span class="flower-ico"><a href="<?php echo url("item/member/ac/subject_add/subbranch_sid/{$detail['sid']}"); ?>">添加分店</a></span>
                <? } ?>
            </em>
            <h3 class="rail-h-3"><?=$detail['name']?>&nbsp;<?=$detail['subname']?></h3>
        </div>

        <div class="body">

            <ul class="field">
                <? if($detail_custom_field) { ?>
                <li>
                    <table class="custom_field" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class='key' align='left'>得分：</td>
                            <td width="*">
                                
<? if(is_array($reviewpot)) { foreach($reviewpot as $val) { ?>
<?=$val['name']?>:<span class="font_2" style="font-size:16px;"><?php echo cfloat($detail[$val['flag']]); ?></span>
<? } } ?>
&nbsp;综合得分:<span class="font_1" style="font-size:16px;"><strong><?php echo cfloat($detail['avgsort']); ?></strong></span>
                            </td>
                        </tr>
                        <?=$detail_custom_field?>
                        <? if($catcfg['useeffect']) { ?>
                        <tr>
                            <td class='key' align='left'>会员参与：</td>
                            <td width="*">
                                <? if($catcfg['effect1']) { ?>
                                有&nbsp;<span id="num_effect1" class="font_2">0</span>&nbsp;位会员<?=$catcfg['effect1']?>(<a href="javascript:post_membereffect(<?=$sid?>,'effect1');">我<?=$catcfg['effect1']?></a>,<a href="javascript:get_membereffect(<?=$sid?>,'effect1','Y');">查看</a>) ,
                                <? } ?>
                                <? if($catcfg['effect2']) { ?>
                                有&nbsp;<span id="num_effect2" class="font_2">0</span>&nbsp;位会员<?=$catcfg['effect2']?>(<a href="javascript:post_membereffect(<?=$sid?>,'effect2');">我<?=$catcfg['effect2']?></a>,<a href="javascript:get_membereffect(<?=$sid?>,'effect2','Y');">查看</a>) .
                                <? } ?>
                            </td>
                        </tr>
                        <? if(!$MOD['close_detail_total']) { ?>
                        <tr>
                            <td class='key' align='left'>数据统计：</td>
                            <td width="*">
                                <span class="font_2"><?=$detail['pageviews']?></span>次浏览,<span class="font_2"><?=$detail['reviews']?></span>条点评,<span class="font_2"><?=$detail['guestbooks']?></span>条留言,<span class="font_2"><?=$detail['pictures']?></span>张图片,<span class="font_2"><?=$detail['favorites']?></span>个收藏<? if($detail['products']) { ?>
,<span class="font_2"><?=$detail['products']?></span>个产品<? } ?>
                            </td>
                        </tr>
                        <? } ?>
                        <? } ?>
                    </table>
                </li>
                <? } ?>
                <li>
                    <a class="abtn1" href="<?php echo url("review/member/ac/add/type/item_subject/id/{$sid}"); ?>"><span><b>我要点评</b></span></a>
                    <a class="abtn2" href="javascript:post_log(<?=$detail['sid']?>);"><span>补充信息</span></a>
                    <a class="abtn2" href="javascript:add_favorite(<?=$detail['sid']?>);"><span>收藏</span></a>
                    <? if(check_module('article')) { ?>
                    <?php $postarticle_url = $detail['ownerid'] && $detail['ownerid'] == $user->uid ? 'ownernews' : 'membernews'; ?>                    <a class="abtn2" href="<?php echo url("article/member/ac/article/op/add/sid/{$detail['sid']}"); ?>"><span>发布资讯</span></a>
                    <? } ?>
                    <div class="clear"></div>
                </li>
            </ul>

            <div class="right">
                <div style="margin:5px 0 10px 0;">
                    <span class="update-img-ico"><a href="<?php echo url("item/member/ac/pic_upload/sid/{$sid}"); ?>">上传图片</a></span>
                    <? if($detail['pictures']) { ?>
                    <span class="view-img-ico"><a href="<?php echo url("item/pic/sid/{$detail['sid']}"); ?>"><?=$detail['pictures']?>图</a></span>
                    <? } ?>
                </div>
                <div class="picture" id="thumb">
                    <a href="<?php echo url("item/pic/sid/{$detail['sid']}"); ?>"><img src="<?=URLROOT?>/<? if($detail['thumb']) { ?>
<?=$detail['thumb']?>
<? } else { ?>
static/images/noimg.gif<? } ?>
" alt="<?=$detail['name']?>" /></a>
                </div>
                <ul class="log">
                    <? if($detail['creator']) { ?>
                    <li>登记:<span class="member-ico"><a href="<?php echo url("space/index/username/{$detail['creator']}"); ?>" title="<?php echo newdate($detail['addtime']); ?>"><?=$detail['creator']?></a></span></li>
                    <? } ?>
                </ul>
            </div>

            <div class="clear"></div>

            <div class="info" id="effect_floor" style="display:none;">
                <em>[<a href="javascript:;" onclick="$('#effect_floor').css('display','none');">关闭</a>]</em>
                <h3><span class="arrow-ico">会员参与</span></h3>
                <p class="members" id="effect"></p>
            </div>

            <? if($MOD['show_thumb'] && $detail['pictures']) { ?>
            <div class="info">
                <em>[<a href="<?php echo url("item/pic/sid/{$detail['sid']}"); ?>">查看全部</a>]&nbsp;[<a href="javascript:;" onclick="$('#itempics').toggle();">收起/展开</a>]</em>
                <h3><span class="arrow-ico"><?=$model['item_name']?>图片</span></h3>
                <div class="itempics" id="itempics"></div>
            </div>
            <? } ?>
            <? if($detail['content']) { ?>
            <div class="info">
                <em>[<a href="javascript:;" onclick="$('#content').toggle();">收起/展开</a>]</em>
                <h3><span class="arrow-ico">详细介绍</span></h3>
                <div class="content" id="content"><?=$detail['content']?></div>
            </div>
            <? } ?>
        </div>
    </div>

    <script type="text/javascript" src="<?=URLROOT?>/static/javascript/review.js"></script>
    <style type="text/css">@import url("<?=URLROOT?>/<?=$_G['tplurl']?>css_review.css");</style>
    <a name="<?=$view?>" id="<?=$view?>"></a>

    <? if($view == 'review') { ?>
        
<? include template('item_subject_detail_review'); ?>
    
<? } elseif($view == 'guestbook') { ?>
        
<? include template('item_subject_detail_guestbook'); ?>
    <? } ?>
</div>

<div id="item_right">

    <? if($detail['video']) { ?>
    <div class="mainrail" style="text-align:center;" id="video_foo"></div>
    <script type="text/javascript">
    $(document).ready(function() {
        var so = new SWFObject("<?=$detail['video']?>", 'video1', 256, 255, 7, '#FFF');
        so.addParam("allowScriptAccess", "always");
        so.addParam("allowFullScreen", "true");
        so.addParam("wmode", "transparent");
        so.write("video_foo");
    });
    </script>
    <? } ?>
    <? if($model['usearea']) { ?>
    <div class="mainrail rail-border-3">
        <?php $mapparam = array(
            'title' => $detail['name'] . $detail['subname'],
            'show' => $detail['mappoint']?1:0,
        ); ?>        <iframe id="item_map" src="<?php echo url("index/map/width/255/height/245/title/{$mapparam['title']}/p/{$detail['mappoint']}/show/{$mapparam['show']}"); ?>" frameborder="0" scrolling="no"></iframe>
        <div style="text-align:center;">
            <? if(!$detail['mappoint']) { ?>
            <a href="javascript:post_map(<?=$detail['sid']?>, <?=$detail['pid']?>);">地图未标注，我来标注</a>
            
<? } else { ?>
            <a href="javascript:show_bigmap();">查看大图</a>&nbsp;
            <a href="javascript:post_map(<?=$detail['sid']?>, <?=$detail['pid']?>);">报错</a>
            <? } ?>
        </div>
    </div>
    <script type="text/javascript">
    function show_bigmap() {
        var src = "<?php echo url("index/map/width/600/height/400/title/{$mapparam['title']}/p/{$detail['mappoint']}/show/{$mapparam['show']}"); ?>";
        var html = '<iframe src="' + src + '" frameborder="0" scrolling="no" width="100%" height="400" id="ifupmap_map"></iframe>';
        dlgOpen('查看大图', html, 600, 450);
    }
    </script>
    <? } ?>
    <? if($_G['modules']['product'] && $detail['products']) { ?>
    <div class="mainrail rail-border-3">
        <em><span class="arrow-ico"><a href="<?php echo url("product/list/sid/{$sid}"); ?>">查看全部</a></span></em>
        <h2 class="rail-h-3 rail-h-bg-3"><?=$model['item_name']?>产品</h2>
        <?php $_G['loader']->variable('datacall');
$_QUERY[$_G['modoer_datacall']['11']['var']] = $_G['datacall']->datacall_sql(11);
 if(empty($_QUERY[$_G['modoer_datacall']['11']['var']])) { include template($_G['modoer_datacall']['11']['empty_tplname'],'datacall'); } else { include template($_G['modoer_datacall']['11']['tplname'],'datacall'); } ?>
    </div>
    <? } ?>
    <? if($detail['coupons'] && check_module('coupon')) { ?>
    <div class="mainrail rail-border-3">
        <em><span class="arrow-ico"><a href="<?php echo url("coupon/list/sid/{$sid}"); ?>">查看全部</a></span></em>
        <h2 class="rail-h-3 rail-h-bg-3">优惠券</h2>
        <ul class="rail-list">
            
<?php $_QUERY['get_val']=$_G['datacall']->datacall_get('list_subject',array('sid'=>"{$sid}",'orderby'=>"dateline DESC",'rows'=>5,),'coupon');
if(is_array($_QUERY['get_val']))foreach($_QUERY['get_val'] as $val_k => $val) { ?>
            <li><cite><?php echo newdate($val['dateline'],'m-d'); ?></cite><a href="<?php echo url("coupon/detail/id/{$val['couponid']}"); ?>" title="<?=$val['subject']?>"><?php echo trimmed_title($val['subject'],16); ?></a></li>
            <?php } ?>
        </ul>
    </div>
    <? } ?>
    <? if(check_module('article')) { ?>
    <div class="mainrail rail-border-3">
        <em><span class="arrow-ico"><a href="<?php echo url("article/list/sid/{$sid}"); ?>">查看全部</a></span></em>
        <h2 class="rail-h-3 rail-h-bg-3">资讯</h2>
        <ul class="rail-list">
            
<?php $_QUERY['get_val']=$_G['datacall']->datacall_get('getlist',array('sid'=>"{$sid}",'orderby'=>"dateline DESC",'rows'=>5,),'article');
if(is_array($_QUERY['get_val']))foreach($_QUERY['get_val'] as $val_k => $val) { ?>
            <li><cite><?php echo newdate($val['dateline'],'m-d'); ?></cite><a href="<?php echo url("article/detail/id/{$val['articleid']}"); ?>" title="<?=$val['subject']?>"><?php echo trimmed_title($val['subject'],16); ?></a></li>
            <?php }if(empty($_QUERY['get_val'])){ ?>
            <li>暂无信息</li>
            <?php } ?>
        </ul>
    </div>
    <? } ?>
    <? if($catcfg['use_subbranch']) { ?>
    <div class="mainrail rail-border-3">
        <h2 class="rail-h-3 rail-h-bg-3">分店</h2>
        <?php $name = _T($detail['name']);
         ?>        <?php $_G['loader']->variable('datacall');
$_QUERY[$_G['modoer_datacall']['3']['var']] = $_G['datacall']->datacall_sql(3);
 if(empty($_QUERY[$_G['modoer_datacall']['3']['var']])) { include template($_G['modoer_datacall']['3']['empty_tplname'],'datacall'); } else { include template($_G['modoer_datacall']['3']['tplname'],'datacall'); } ?>
    </div>
    <? } ?>
    <div class="mainrail rail-border-3">
        <em>
            <span class="selected" id="btn_subject1" onclick="tabSelect(1,'subject')">同类<?=$model['item_name']?></span>
            <? if($model['usearea']) { ?>
            <span class="unselected" id="btn_subject2" onclick="tabSelect(2,'subject')">附近<?=$model['item_name']?></span>
            <? } ?>
        </em>
        <h2 class="rail-h-3 rail-h-bg-3">相关<?=$model['item_name']?></h2>

        <div class="none" id="subject1" datacallname="主题_同类主题" params="{'catid':'<?=$detail['catid']?>','sid':'<?=$sid?>'}"></div>
        <? if($model['usearea']) { ?>
        <div class="none" id="subject2" datacallname="主题_附近主题" params="{'aid':'<?=$detail['aid']?>','sid':'<?=$sid?>'}"></div>
        <? } ?>
    </div>
    <script type="text/javascript">
    $(document).ready(function() {
        tabSelect(1,'subject');
    });
    </script>

</div>

<div class="clear"></div>

</div>
<div id="adv_1_content" style="display:none;">
<? if($_incfile_=display('adv:show','name/主题内容页_点评间广告'))include_once($_incfile_);?>
</div>
<script type="text/javascript">
//加载广告
replace_content('adv_1=adv_1_content');
</script><?php footer(); ?>