<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<div id="body">
<?=form_begin(cpurl($module,$act))?>
    <div class="space">
        <div class="subtitle"><?=$MOD['name']?>&nbsp;模块配置</div>
        <ul class="cptab">
            <li class="selected" id="btn_config1"><a href="#" onclick="tabSelect(1,'config');" onfocus="this.blur()">功能配置</a></li>
            <li id="btn_config2"><a href="#" onclick="tabSelect(2,'config');" onfocus="this.blur()">显示配置</a></li>
            <li id="btn_config3"><a href="#" onclick="tabSelect(3,'config');" onfocus="this.blur()">点评分数设置</a></li>
        </ul>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0" id="config1">
            <tr>
                <td class="altbg1"><strong>Meta Keywords:</strong>Keywords 项出现在页面头部的 Meta 标签中，用于记录本页面的关键字，多个关键字间请用半角逗号 "," 隔开</td>
                <td><input type="text" name="modcfg[meta_keywords]" value="<?=$modcfg['meta_keywords']?>" class="txtbox" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>Meta Description:</strong>Description 出现在页面头部的 Meta 标签中，用于记录本页面的概要与描述。</td>
                <td><input type="text" name="modcfg[meta_description]" value="<?=$modcfg['meta_description']?>" class="txtbox" /></td>
            </tr>
            <tr>
                <td width="45%" class="altbg1" valign="top"><strong>表单验证码:</strong>开启验证码可减少广告机提交信息，但是也会让会员感到繁琐</td>
                <td>
                    <div>发布点评(会员):<?=form_bool('modcfg[seccode_review]', $modcfg['seccode_review'])?></div>
                    <div>发布点评(游客):<?=form_bool('modcfg[seccode_review_guest]', $modcfg['seccode_review_guest'])?></div>
                </td>
            </tr>
            <tr>
                <td class="altbg1"><strong>点评内容字数限制 </strong>定义点评内容的字符限制</td>
                <td><input type="text" name="modcfg[review_min]" value="<?=$modcfg['review_min']?>" class="txtbox5" /> - <input type="text" name="modcfg[review_max]" value="<?=$modcfg['review_max']?>" class="txtbox5" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>回应内容字数限制</strong>定义回应内容字符限制</td>
                <td><input type="text" name="modcfg[respond_min]" value="<?=$modcfg['respond_min']?>" class="txtbox5" /> - <input type="text" name="modcfg[respond_max]" value="<?=$modcfg['respond_max']?>" class="txtbox5" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>审核回应内容:</strong>开启审核功能后，未审核的信息将暂时不在前台显示和操作。</td>
                <td>
                    <?=form_bool('modcfg[respondcheck]', $modcfg['respondcheck'])?>
                </td>
            </tr>
            <tr>
                <td class="altbg1">
                    <strong>启用非默认头像点评</strong>用户必须设置一个非默认头像后才能进行点评
                </td>
                <td><?=form_bool('modcfg[avatar_review]', $modcfg['avatar_review'])?></td>
            </tr>
            <tr>
                <td class="altbg1">
                    <strong>兼容空格标签分隔符</strong>兼容1.x中使用空格分类符号，开启后可以使用空格来实现分隔标签. 注意:空格会切断英文短语的标签。
                </td>
                <td><?=form_bool('modcfg[tag_split_sp]', $modcfg['tag_split_sp'])?></td>
            </tr>
        </table>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0" id="config2" style="display:none;">
            <tr>
               <td width="45%" class="altbg1"><strong>回应显示数:</strong>回应中每页显示回应数目</td>
                <td><?=form_radio('modcfg[respond_num]',array('5'=>'5条','10'=>'10条','20'=>'20条'),$modcfg['respond_num'])?></td>
            </tr>
        </table>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0" id="config3" style="display:none;">
            <tr>
                <td width="45%"  valign="top" class="altbg1"><strong>总分形式：</strong>列表页和详细页面显示主题的各个评分项的数值形式。默认为百分制。</td>
                <td width="*"><?=form_select('modcfg[scoretype]',array('100'=>'百分制','10'=>'十分制','5'=>'五分制'),$modcfg['scoretype'])?></td>
            </tr>
            <tr>
                <td valign="top" class="altbg1"><strong>分数小数点：</strong>各项得分的显示是否显示小数点。</td>
                <td><?=form_select('modcfg[decimalpoint]',array('0'=>'不显示','1'=>'1位','2'=>'2位'),$modcfg['decimalpoint'])?></td>
            </tr>
        </table>
    </div>
    <center><?=form_submit('dosubmit',lang('admincp_submit'),'yes','btn')?></center>
<?=form_end()?>
</div>