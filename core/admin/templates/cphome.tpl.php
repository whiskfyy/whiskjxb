<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<script type="text/javascript" src="./static/javascript/mdialog.js"></script>
<div id="body">

    <?if($admin->check_access()):?>
    <div class="space" id="message" style="display:none;">
        <div class="subtitle">系统提示</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
            <?if($install_exists):?>
            <tr><td><span class="font_4">请尽快删除系统目录下的 install.php 和 install 目录，以免被他人非法利用。</span></td></tr>
            <?endif;?>
            <?if(DEBUG):?>
            <tr><td>
            <span class="font_4">您的系统目前开启了调试模式(DEBUG)，如果您的网站已经正式上线，请关闭调试模式(DEBUG)。</span><br />
            <span class="font_2">使用专业编辑器打开 core/init.php 文件，找到 define('DEBUG', TRUE); 修改为 define('DEBUG', FALSE);</span>
            </td></tr>
            <?endif;?>
            <?if($_G['modify_template']):?>
            <tr><td>
            <span class="font_4">您的系统目前开启了模板在线编辑功能，如果您的网站已经正式上线，请关闭本功能。</span><br />
            <span class="font_2">使用专业编辑器打开 data/config.php 文件，找到 $_G['modify_template'] = TRUE; 修改为 $_G['modify_template'] = FALSE;</span>
            </td></tr>
            <?endif;?>
            <?if(!$server['gd']):?>
            <tr><td><span class="font_4">您的系统未加载GD库，将无法处理图片，强烈建议您在PHP.INI中加载。</span></td></tr>
            <?endif;?>
        </table>
    </div>
    <?endif;?>

    <div class="space">
        <div class="subtitle">后台在线用户</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
            <tr class="altbg1">
                <td width="30%">用户名称</td>
                <td width="40%">IP地址</td>
                <td width="30%">最后活动时间</td>
            </tr>
            <?php if($sessions) while($val = $sessions->fetch_array()) { ?>
            <?php if($val['adminname']==$admin->adminname){?><tr style="font-weight:bold;"><?}else{?><tr><?}?>
                <td><?=$val['adminname']?></td>
                <td><?=$val['ip']?></td>
                <td><?=date('Y-m-d H:i:s', $val['dateline'])?></td>
            </tr>
            <? } ?>
        </table>
    </div>

    <?if($_G['cfg']['console_total']):?>
    <div class="space">
        <div class="subtitle">数据统计</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
          <?if($system):foreach($system as $key => $val):?>
          <?if($key%3==0):?><tr><?endif;?>
            <td width="100" class="altbg1"><?=$val['name']?></td>
            <td width="200"><?=$val['content']?></td>
          <?if($key%3==2):?></tr><?endif;?>
          <?endforeach;?>
          <?php
            $ix=ceil(($key+1)/3) * 3 - ($key+1);
            for($i=0;$i<$ix;$i++) {
                echo '<td width="100" class="altbg1">&nbsp;</td>';
                echo '<td width="200">&nbsp;</td>';
            }
            echo '</tr>';
          ?>
          <?else:?>
            <tr><td>没有统计信息。</td></tr>
          <?endif;?>
        </table>
    </div>
    <?endif;?>

    <div class="space">
        <div class="subtitle">系统信息</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
            <tr id="trNewversion" style="display:none;"><td colspan="2">---</td></tr>
            <tr><td class="altbg1">程序版本:</td><td><?=$_G['modoer']['version']?> Build <?=$_G['modoer']['build']?> &nbsp;[&nbsp;<a href="http://bbs.modoer.com/forumdisplay.php?fid=4" target="_blank">查看最新版本</a>&nbsp;]&nbsp;<span id="mo_user"></span></td></tr>
            <tr><td class="altbg1">程序编码:</td><td><?=$_G['charset']?></td></tr>
            <tr><td class="altbg1">服务器解译引擎:</td><td><?=$server['software']?></td></tr>
            <tr><td class="altbg1">PHP版本</td><td><?=$server['phpver']?>&nbsp;<?if(substr($server['phpver'],0,1)<5)echo'(<span class="font_1">强烈建议升级到PHP 5.x</span>)'?></td></tr>
            <tr><td class="altbg1">MySQL版本</td><td><?=$server['mysqlver']?>&nbsp;<?if(substr($server['mysqlver'],0,1)<5)echo'(<span class="font_1">建议升级到MySQL 5.x</span>)'?></td></tr>
            <tr><td width="200" class="altbg1">服务器时间:</td><td><?=$server['time']?></td></tr>
            <tr><td class="altbg1">文件上传:</td><td><?=$server['upfile']?></td></tr>
            <tr><td class="altbg1">全局变量 register_globals:</td><td><?=ini_get('register_globals') ? '开启' : '关闭'?>(建议关闭)</td></tr>
            <tr><td class="altbg1">安全模式 safe_mode:</td><td><?=ini_get('safe_mode') ? '开启' : '关闭'?></td></tr>
            <tr><td class="altbg1">图形处理 GD Library:</td><td><?=$server['gd']?></td></tr>
            <tr><td class="altbg1">Magic_Quotes_Gpc:</td><td><?=ini_get('magic_quotes_gpc') ? '开启' : '关闭'?></td></tr>
            <tr><td class="altbg1">是否允许打开远程连接:</td><td><?php echo ini_get("allow_url_fopen") ? '支持' : '不支持'?>(反向整合需要)</td></tr>
            <tr><td class="altbg1">内存占用:</td><td><?=$server['memory']?> KB</td></tr>
        </table>
    </div>

    <div class="space">
        <div class="subtitle"><?=$_G['modoer']['name']?> 开发团队</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
            <tr><td width="200" class="altbg1">版权所有:</td><td>Moufer Studio</td></tr>
            <tr><td class="altbg1">程序开发:</td><td><a href="http://bbs.modoer.com/space-uid-1.html" target="_blank">moufer</a></td></tr>
            <tr><td class="altbg1">美工设计:</td><td><a href="http://bbs.modoer.com/space-uid-1.html" target="_blank">moufer</a>,zqq</td></tr>
            <tr><td class="altbg1">官方网站:</td><td><a href="http://bbs.modoer.com" target="_blank">http://www.modoer.com</a></td></tr>
            <tr><td class="altbg1">技术支持论坛:</td><td><a href="http://bbs.modoer.com" target="_blank">http://bbs.modoer.com</a></td></tr>
        </table>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    if($("#message table tr").length > 0) {
        $('#message').css('display','');
    }
}); 
</script>
<script type="text/javascript" src="http://www.modoer.cn/version.php?<?=$urls?>"></script>