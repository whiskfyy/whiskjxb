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
                <h3>我的回应</h3>
                <div class="mainrail">
                    <form method="post" name="myform" action="<?php echo url("review/member/ac/{$ac}/rand/{$_G['random']}"); ?>">
                    <table width="100%" cellspacing="0" cellpadding="0" class="maintable" trmouse="Y">
                        <tr>
                            <th width="25" align="center"><input type="checkbox" onclick="checkbox_checked('respondids[]',this);" /></th>
                            <th width="*">内容</th>
                            <th width="60">状态</th>
                            <th width="120">时间</th>
                            <th width="100">编辑</th>
                        </tr>
                        <? if($total) { ?>
                        
<? if($list) { while($val = $list->fetch_array()) { ?>
                        <tr>
                            <td><input type="checkbox" name="respondids[]" value="<?=$val['respondid']?>" /></td>
                            <td><?=$val['content']?></td>
                            <td><? if($val['status']) { ?>
已审核
<? } else { ?>
未审核<? } ?>
</td>
                            <td><?php echo newdate($val['posttime'],'Y-m-d H:i'); ?></td>
                            <td><a href="<?php echo url("review/member/ac/m_respond/op/edit/respondid/{$val['respondid']}"); ?>">编辑</a></td>
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
                        <button type="button" onclick="easy_submit('myform','delete','respondids[]');">删除所选</button>
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