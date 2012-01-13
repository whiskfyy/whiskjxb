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
<li class="act"><a href="<?php echo url("item/member/ac/pic_upload"); ?>">上传图片</a></li></ul><div class="clear"></div>
                <form method="post" name="myform" action="<?php echo url("item/member/ac/{$ac}/rand/{$_G['random']}"); ?>">
                <table width="100%" cellspacing="0" cellpadding="0" class="maintable" trmouse="Y">
                    <tr>
                        <th width="20" align="center"><input type="checkbox" onclick="checkbox_checked('picids[]',this);" /></th>
                        <th width="110">图片</th>
                        <th width="*">标题/说明</th>
                        <th width="80">尺寸/大小</th>
                        <th width="60">状态</th>
                        <th width="150"><?=$model['item_name']?>名称/上传时间</th>
                        <th width="50">操作</th>
                    </tr>
                    <? if($total) { ?>
                    
<? if($list) { while($val = $list->fetch_array()) { ?>
                    <?php if(isset($val['name'])) $val_name = substr($val['name'],0,10).'********';
						 ?>                    <tr>
                        <td><input type="checkbox" name="picids[]" value="<?=$val['picid']?>" /></td>
                        <td class="picthumb"><a href="<?=URLROOT?>/<?=$val['filename']?>" target="_blank"><img src="<?=URLROOT?>/<?=$val['thumb']?>" width="120" style="padding:5px 0;" /></a></td>
                        <td>
                            <input type="text" class="t_input" style="width:198px;" value="<?=$val['title']?>" name="picture[<?=$val['picid']?>][title]" /><br />
                            <textarea name="picture[<?=$val['picid']?>][comments]" style="width:200px;height:40px;margin-top:2px;"><?=$val['comments']?></textarea>
                        </td>
                        <td><?=$val['width']?>×<?=$val['height']?><br /><?php echo round($val['size']/1024); ?> KB</td>
                        <td><? if($val['status']) { ?>
已审核
<? } else { ?>
<span class="font_1">未审核</span><? } ?>
</td>
                        <td>
                            <a href="<?php echo url("item/detail/id/{$val['sid']}"); ?>" target="_blank"><?=$val_name?>&nbsp;<?=$val['subname']?></a><br /><?php echo newdate($val['addtime'], 'Y-m-d H:i'); ?>                       </td>
                       <td><a href="<?php echo url("item/member/ac/pic_upload/sid/{$val['sid']}"); ?>">上传</a></td>
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
                    <input type="hidden" name="op" value="update" />
                    <button type="button" onclick="easy_submit('myform','update',null);">更新修改</button>&nbsp;
                    <button type="button" onclick="easy_submit('myform','delete','picids[]');">删除所选</button>
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