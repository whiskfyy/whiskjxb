<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<script type="text/javascript">
function collapsing(areacode) {
    var len = 3 + areacode.length;
    $("table tr").each(function(i) {
        if(this.id!='' && this.id.length>len && this.id.substr(0,len)=='tr_'+areacode) {
            this.style.display = this.style.display=='none' ? '' : 'none';
        }
    });
}
</script>
<div id="body">
    <form method="post" action="<?=cpurl($module,$act,$op)?>&">
    <div class="space">
        <div class="subtitle">地区管理：第<?=$level?>层&nbsp;<?=$detail['name']?></div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0" trmouse="Y">
            <tr class="altbg1">
                <td width="60">序号</td>
                <td width="40">编号</td>
                <td width="200">名称</td>
                <td width="*">地图中心坐标</td>
                <td width="200">操作</td>
            </tr>
            <? if($list) {?>
            <? foreach($list as $val) { ?>
            <tr>
                <td><input type="text" class="txtbox5" name="area[<?=$val['aid']?>][listorder]" value="<?=$val['listorder']?>" /></td>
                <td><?=$val['aid']?></td>
                <td><?=$val['name']?></td>
                <td><?=$val['mappoint']?></td>
                <td>
                    <a href="<?=cpurl($module,$act,'edit',array('aid' => $val['aid']))?>">编辑</a>&nbsp;
                    <?if($level > 1) :?>
                    <a href="<?=cpurl($module,$act,'delete',array('aid' => $val['aid']))?>" onclick="return confirm('您确定要删除吗？');">删除</a>&nbsp;
                    <?endif;?>
                    <?if($level < 3) :?>
                    <a href="<?=cpurl($module,$act,'add',array('pid' => $val['aid']))?>">添加下级</a>&nbsp;
                    <a href="<?=cpurl($module,$act,'',array('pid' => $val['aid']))?>">查看下级</a>
                    <?endif;?>
                </td>
            </tr>
            <? } ?>
            <? } else { ?>
            <tr><td colspan="4">暂无信息。</td></tr>
            <? } ?>
        </table>
    </div>
    <center>
        <? if($list) {?>
        <input type="submit" name="dosubmit" value=" 更新编辑 " class="btn" />&nbsp;
        <? } ?>
        <? if($level>1) :?>
        <input type="button" value=" 返回上一级 " class="btn" onclick="document.location.href='<?=cpurl($module,$act,'',array('pid'=>$detail['pid']))?>'" />
        <? endif; ?>
    </center>
</form>
</div>