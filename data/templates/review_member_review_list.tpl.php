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
            <h3>我的点评</h3>
            <div class="mainrail">
                <form method="post" name="myform" action="<?php echo url("review/member/ac/{$ac}/rand/{$_G['random']}"); ?>">
                <ul class="optabs">
<? if(is_array($R->idtypes)) { foreach($R->idtypes as $key => $val) { ?>
<li<? if($key==$idtype) { ?>
 class="active"<? } ?>
><a href="<?php echo url("review/member/ac/{$ac}/idtype/{$key}"); ?>"><?=$val['name']?></a></li>
<? } } ?>
<li class="act"><a href="<?php echo url("review/member/ac/add"); ?>">我要点评</a></li></ul><div class="clear"></div>
                <table width="100%" cellspacing="0" cellpadding="0" class="maintable" trmouse="Y">
                    <tr>
                        <th width="300">点评对象</th>
                        <th width="40">鲜花</th>
                        <th width="40">回应</th>
                        <th width="60">状态</th>
                        <th width="120">点评时间</th>
                        <th width="*">编辑</th>
                    </tr>
                    <? if($total) { ?>
                    
<? if(is_array($resList)) { foreach($resList as $val) { ?>
                    <tr>
                        <td><a href="<?php echo url("review/detail/id/{$val['rid']}"); ?>" target="_blank"><?=$val['subject']?></a></td>
                        <td><?=$val['flowers']?></td>
                        <td><?=$val['responds']?></td>
                        <td><? if($val['status']) { ?>
已审核
<? } else { ?>
未审核<? } ?>
</td>
                        <td><?php echo newdate($val['posttime'],'Y-m-d H:i'); ?></td>
                        <td><a href="<?php echo url("review/member/ac/edit/rid/{$val['rid']}"); ?>">编辑</a>&nbsp;<a href="<?php echo url("review/member/ac/review/op/delete/rid/{$val['rid']}"); ?>" onclick="return confirm('您确定要进行删除操作吗？');">删除</a></td>
                    </tr>
                    
<? } } ?>
                    
<? } else { ?>
                    <tr>
                        <td colspan="7">暂无信息。</td>
                    </tr>
                    <? } ?>
                </table>
                <div class="multipage"><?=$multipage?></div>
                </form>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<div class="mybottom"></div>
</div><?php footer(); ?>