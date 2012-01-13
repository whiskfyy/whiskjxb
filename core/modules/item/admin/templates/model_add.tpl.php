<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<div id="body">
<form method="post" action="<?=cpurl($module,$act)?>">
    <div class="space">
        <div class="subtitle"><?=$subtitle?></div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="45%" class="altbg1"><strong>模型名称：</strong></td>
                <td width="55%"><input type="text" name="t_model[name]" class="txtbox2" value="<?=$t_model['name']?>" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>附加数据表:</strong>每一个模型的自定义字段将独立在一张新建的表中；<br />数据表名称由英文字母[a-z]或数字[0-9]组成。<br /><span class="font_1">此项填写后将无法再次改动</span></td>
                <td>
                    <?if($disabled){?>
                        <?=$_G['dns']['dbpre'].$t_model['tablename']?>
                    <? }else{?>
                        <?=$_G['dns']['dbpre']?>subject_<input type="text" name="t_model[tablename]" class="txtbox3" />
                    <?}?>
                </td>
            </tr>
            <tr>
                <td class="altbg1"><strong>启用地理特称:</strong>点评对象是否有地理特征，将使用到地图标注等功能。<br /><span class="font_1">此项填写后将无法再次改动</span></td>
                <td><input type="radio" name="t_model[usearea]" value="1"<?if(empty($t_model)||$t_model['usearea'])echo' checked="checked"';echo$disabled;?> /> 是&nbsp;&nbsp;<input type="radio" name="t_model[usearea]" value="0"<?if(!$t_model['usearea'])echo' checked="checked"';echo$disabled;?> /> 否</td>
            </tr>
            <tr>
                <td class="altbg1"><strong>点评对象名称:</strong>即点评的内容的总称，例如商铺，企业，手机，游戏等</td>
                <td><input type="text" name="t_model[item_name]" class="txtbox2" value="<?=$t_model['item_name']?>" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>点评对象单位:</strong>点评对象的数量计量单位，例如商铺：户、家，手机：部</td>
                <td><input type="text" name="t_model[item_unit]" class="txtbox2" value="<?=$t_model['item_unit']?>" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>列表页模板文件:</strong>显示点评对象列表的模板文件，无需填写模板名称的后缀名<br />默认请填写 item_subject_list</td>
                <td><input type="text" name="t_model[tplname_list]" class="txtbox2" value="<?=$t_model['tplname_list']?>" /><?=$_config['tplext']?></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>内容页模板文件:</strong>显示点评对象详细信息的模板文件，可留空，仅在不使用独立风格时有效<br />默认请填写 item_subject_detail</td>
                <td><input type="text" name="t_model[tplname_detail]" class="txtbox2" value="<?=$t_model['tplname_detail']?>" /><?=$_config['tplext']?></td>
            </tr>
        </table>
        <center>
            <?if($modelid>0){?><input type="hidden" name="modelid" value="<?=$modelid?>" /><?}?>
            <button type="submit" name="dosubmit" value="yes" class="btn" /> 提 交 </button>&nbsp;
            <button type="button" class="btn" onclick="history.go(-1);" /> 返 回 </button>
        </center>
    </div>
</form>
</div>