<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<div id="body">
<?=form_begin(cpurl($module,$act))?>
    <div class="space">
        <div class="subtitle"><?=$MOD['name']?>&nbsp;模块配置</div>
        <ul class="cptab">
            <li class="selected" id="btn_config1"><a href="#" onclick="tabSelect(1,'config');" onfocus="this.blur()">功能设置</a></li>
        </ul>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0" id="config1">
            <tr>
                <td class="altbg1" valign="top" width="45%"><strong>启用验证码:</strong>设置验证码，防止恶意注册 </td>
                <td width="*">
                    <div>注册:<?=form_bool('modcfg[seccode_reg]', $modcfg['seccode_reg'])?></div>
                    <div>登录:<?=form_bool('modcfg[seccode_login]', $modcfg['seccode_login'])?></div>
                </td>
            </tr>
            <tr>
                <td class="altbg1"><strong>关闭新用户注册:</strong>设置是否关闭游客注册成为网站会员</td>
                <td><?=form_radio('modcfg[closereg]',array(1=>lang('admincp_yes'),0=>lang('admincp_no')),$modcfg['closereg'])?></td>
            </tr>
            <tr>
                <td class="altbg1" valign="top"><strong>用户信息保留关键字:</strong>用户在其用户信息(如用户名、昵称、自定义头衔等)中无法使用这些关键字。每个关键字一行，可使用通配符 "*" 如 "*版主*"(不含引号)</td>
                <td><?=form_textarea('modcfg[censoruser]',$modcfg['censoruser'],5,50)?></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>允许同一&nbsp;Email&nbsp;注册不同用户:</strong>选择“否”将只允许一个 Email 地址只能注册一个用户名</td>
                <td><?=form_radio('modcfg[existsemailreg]',array(1=>lang('admincp_yes'),0=>lang('admincp_no')),$modcfg['existsemailreg'])?></td>
            </tr>
            <tr>
                <td class="altbg1">
                    <strong>显示许可协议:</strong>新用户注册时显示许可协议
                </td>
                <td>
                    <input type="radio" name="modcfg[showregrule]" value="1"<?if($modcfg['showregrule'])echo' checked';?> onclick="document.getElementById('tr_regrule').style.display='';" /> 是&nbsp;&nbsp;<input type="radio" name="modcfg[showregrule]" value="0"<?if(!$modcfg['showregrule'])echo' checked';?> onclick="document.getElementById('tr_regrule').style.display='none';" />否
                </td>
            </tr>
            <tr id="tr_regrule"<?if(!$modcfg['showregrule'])echo' style="display:none;"';?> valign="top">
                <td class="altbg1"><strong>许可协议内容:</strong>新用户注册时显示许可协议</td>
                <td><textarea name="modcfg[regrule]" rows="8" cols="40"><?=$modcfg['regrule']?></textarea></td>
            </tr>
            <tr>
                <td class="altbg1" valign="top"><strong>发送欢迎信息:</strong>可选择是否自动向新注册用户发送一条欢迎信息</td>
                <td>
                    <input type="radio" name="modcfg[salutatory]" value="0"<?if(empty($modcfg['salutatory']))echo' checked';?> onclick="document.getElementById('tr_salutatory_msg').style.display='none';" /> 不发送&nbsp;
                    <input type="radio" name="modcfg[salutatory]" value="1"<?if($modcfg['salutatory'])echo' checked';?> onclick="document.getElementById('tr_salutatory_msg').style.display='';" /> 发送欢迎短消息
                </td>
            </tr>
            <tr id="tr_salutatory_msg"<?if(empty($modcfg['salutatory']))echo' style="display:none;"';?>>
                <td class="altbg1" valign="top">
                    <strong>欢迎信息内容:</strong>可用变量说明：<br />网站名：$sitename<br />注册会员名：$username<br />当前时间：$time
                </td>
                <td><?=form_textarea('modcfg[salutatory_msg]',$modcfg['salutatory_msg'],8,40)?></td>
            </tr>
        </table>
    </div>
    <center><?=form_submit('dosubmit',lang('admincp_submit'),'yes','btn')?></center>
<?=form_end()?>
</div>