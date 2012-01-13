<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<style type="text/css">
.img img { max-width:80px; max-height:60px; border:1px solid #ccc; padding:1px; 
    _width:expression(this.width > 80 ? 80 : true); _height:expression(this.height > 60 ? 60 : true); }
</style>
<div id="body">
<form method="post" name="myform" action="<?=cpurl($module,$act,'',array('pid'=>$pid))?>">
    <div class="space">
        <div class="subtitle">主题管理</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0" trmouse="Y">
            <tr class="altbg2"><th colspan="12">
                <ul class="subtab">
                    <?foreach($_G['loader']->variable('category',MOD_FLAG) as $key => $val) { if($val['pid']) continue; ?>
                    <li<?=$pid==$key?' class="current"':''?>><a href="<?=cpurl($module,$act,'',array('pid'=>$key))?>"><?=$val['name']?></a></li>
                    <?}?>
                </ul>
            </th></tr>
            <tr class="altbg1">
                <td width="25">选?</td>
                <td width="90">封面</td>
                <td width="*"><?=($title.'&nbsp;'.$subtitle)?>/<?=$cattitle?><?if($model['usearea']):?>/地区<?endif;?></td>
                <td width="60">推荐度<?=p_order('finer');?></td>
                <td width="40"><center>等级<?=p_order('level');?></center></td>
                <td width="40"><center>点评<?=p_order('reviews');?></center></td>
                <td width="40"><center>图片<?=p_order('pictures');?></center></td>
                <td width="40"><center>留言<?=p_order('guestbooks');?></center></td>
                <td width="50"><center>浏览<?=p_order('pageviews');?></center></td>
                <td width="110">添加时间<?=p_order('addtime');?></td>
                <td width="50">状态<?=p_order('finer');?></td>
                <td width="30">操作</td>
            </tr>
            <?if($total):?>
            <?while($val=$list->fetch_array()):?>
            <tr>
                <td><input type="checkbox" name="sids[]" value="<?=$val['sid']?>" /></td>
                <td class="img"><img src="<?if($val['thumb']):?><?=$val['thumb']?><?else:?>static/images/s_noimg.gif<?endif;?>" /></td>
                <td>
                    <div><a href="<?=url("item/detail/id/$val[sid]")?>" target="_blank"><b><?=trim($val['name'].' '.$val['subname'])?></b></a></div>
                    <div><?=$category[$val['pid']]['name']?> &raquo; <?=template_print('item','category',array('catid'=>$val['catid']))?></div>
                    <?if($model['usearea']):?><div><?=template_print('modoer','area',array('aid'=>$val['aid']))?></div><?endif;?>
                </td>
                <td><input type="text" name="subjects[<?=$val['sid']?>][finer]" value="<?=$val['finer']?>" class="txtbox5" /></td>
                <td><center><?=$val['level']?></center></td>
                <td><center><a href="<?=cpurl('review','review','list',array('idtype'=>'item','id'=>$val['sid'],'dosubmit'=>'yes'))?>"><?=$val['reviews']?></a></center></td>
                <td><center><a href="<?=cpurl($module,'picture_list','',array('pid'=>$val['pid'],'sid'=>$val['sid']))?>"><?=$val['pictures']?></a></center></td>
                <td><center><a href="<?=cpurl($module,'guestbook_list','',array('pid'=>$val['pid'],'sid'=>$val['sid']))?>"><?=$val['guestbooks']?></a></center></td>
                <td><center><?=$val['pageviews']?></center></td>
                <td><?=date('Y-m-d H:i', $val['addtime'])?></td>
                <td><?=$val['status']==1?'正常':'未知'?></td>
                <td>
                    <a href="<?=cpurl($module,'subject_edit','',array('pid'=>$pid, 'sid'=>$val['sid']))?>">编辑</a>
                </td>
            </tr>
            <?endwhile;?>
            <tr>
                <td colspan="12" class="altbg1">
                    <button type="button" onclick="checkbox_checked('sids[]');" class="btn2">全选</button>
                    <input type="checkbox" name="delete_point" id="delete_point" value="1" /><label for="delete_point">删除主题同时减少登记者积分</label>&nbsp;&nbsp;
                    转移同模型分类：<select name="moveto_catid">
                        <option value="" selected="selected">==选择转移目标==</option>
                        <?=form_item_category_equal_model($pid);?>&nbsp;
                        </select>
                </td>
            </tr>
            <?else:?>
            <tr>
                <td colspan="15">暂无信息。</td>
            </tr>
            <?endif;?>
        </table>
    </div>
    <?if($total):?>
    <div class="multipage"><?=$multipage?></div>
    <center>
        <input type="hidden" name="dosubmit" value="yes" />
        <input type="hidden" name="op" value="" />
        <button type="button" class="btn" onclick="easy_submit('myform','update',null)">更新修改</button>&nbsp;
        <button type="button" class="btn" onclick="easy_submit('myform','move','sids[]')">批量转移分类</button>&nbsp;
        <button type="button" class="btn" onclick="easy_submit('myform','rebuild','sids[]')">重建统计</button>&nbsp;
        <button type="button" class="btn" onclick="easy_submit('myform','delete','sids[]')">删除所选</button>
    </center>
    <?endif;?>
</form>
<form method="get" action="<?=SELF?>" style="margin-top:10px;">
    <input type="hidden" name="module" value="<?=$module?>" />
    <input type="hidden" name="act" value="<?=$act?>" />
    <input type="hidden" name="pid" value="<?=$pid?>" />
    <div class="space">
        <div class="subtitle">搜索</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="altbg1" width="120"><?=$cattitle?></td>
                <td width="*">
                    <select name="catid">
                        <option value="">全部</option>
                        <?=form_item_category_sub($pid, $_GET['catid'])?>
                    </select>
                </td>
            </tr>
            <?if($model['usearea']):?>
            <tr>
                <td class="altbg1">地区</td>
                <td><select name="aid">
                        <option value="">全部</option>
                        <?=form_area($_GET['aid'])?>
                    </select>
                </td>
            </tr>
            <?endif;?>
            <tr>
                <td class="altbg1">排序</td>
                <td>
                    <?=form_select('order', array('addtime'=>'登记时间','finer'=>'推荐度','level'=>'等级','reviews'=>'点评数量','guestooks'=>'留言数量','pictures'=>'图片数量','pageviews'=>'浏览量'), $_GET['order'])?>
                    <?=form_select('by', array('DESC'=>'倒序','ASC'=>'顺序'), $_GET['by'])?>
                </td>
            </tr>
            <tr>
                <td class="altbg1">关键字</td>
                <td><input type="text" name="keyword" class="txtbox2" value="<?=$_GET['keyword']?>" />&nbsp;&nbsp;
                <button type="submit" class="btn2">搜索</button></td>
            </tr>
        </table>
    </div>
</form>
</div>