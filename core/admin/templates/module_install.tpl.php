<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<div id="body">
<?=form_begin(cpurl($module,$act,$op))?>
    <?if($step=='1') {?>
    <div class="space">
        <div class="subtitle">模块安装：填写模块所在目录</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="50%" class="altbg1"><strong>模块所在目录:</strong>模块必须放在 ./core/modules 下面，然后填写模块的目录名称，例如：product</td>
                <td width="*"><input type="text" name="directory" class="txtbox2" /></td>
            </tr>
        </table>
        <input type="hidden" name="step" value="2" />
        <center><button type="submit" value="dosubmit" class="btn">下一步</button></center>
    </div>
    <?} elseif($step=='2') {?>
    <div class="space">
        <div class="subtitle">模块安装：填写模块所在目录</div>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
            <?foreach($newmodule as $key => $val) {?>
            <tr>
                <td width="20%" class="altbg1"><strong><?=$key?></strong></td>
                <td width="*"><input type="text" name="newmodule[<?=$key?>]" value="<?=$val?>" class="txtbox" <?if(isset($readonly[$key]))echo'readonly ';?>/></td>
            </tr>
            <?}?>
        </table>
        <input type="hidden" name="step" value="3" />
        <input type="hidden" name="newmodule[directory]" value="<?=$_POST['directory']?>" />
        <center>
            <button type="button" class="btn" onclick="history.go(-1);">上一步</button>&nbsp;
            <button type="submit" class="btn" value="modulessubmit">安装模块</button>
        </center>
    </div>
    <?}?>
</form>
</div>