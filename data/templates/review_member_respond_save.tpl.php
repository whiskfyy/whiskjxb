<? !defined('IN_MUDDER') && exit('Access Denied'); ?><?php $myid = $MOD['respondcheck'] ? 'CHECK' : "$rid|POST";
 if(!defined('IN_AJAX')) { include template('modoer_header'); ?>
<div id="body">
<div class="myhead"></div>
<div class="mymiddle">
    <div class="myleft">
        
<? include template('menu','member','member'); ?>
    </div>
    <div class="myright">
        <div class="myright_top"></div>
        <div class="myright_middle">
            <h3>编辑回应</h3><? } ?>
            <div class="mainrail">
                <form method="post"name="frm_respond" id="frm_respond" action="<?php echo url("review/member/ac/{$ac}/op/save/in_ajax/{$_G['in_ajax']}"); ?>">
                <table width="100%" cellspacing="0" cellpadding="0" class="maintable">
                    <tr>
                        <td>回应对象：
                            <? if($uid) { ?>
                            <a href="<?php echo url("space/index/uid/{$review['uid']}"); ?>" target="_blank"><?=$review['username']?></a>
                            
<? } else { ?>
                            <span style="color:#808080;">游客(<?php echo preg_replace("/^([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)$/","\\1.\\2.*.*", $review['ip']); ?>)</span>
                            <? } ?>
                        </td>
                    </tr>
                    <? if(!defined('IN_AJAX')) { ?>
                    <tr>
                        <td>点评内容：
                            <span style="color:#808080;"><?php echo nl2br($review['content']); ?></span>
                        </td>
                    </tr>
                    <? } ?>
                    <tr>
                        <td>回应内容：(字数限制：<?=$MOD['respond_min']?> - <?=$MOD['respond_max']?>，当前字数：<span id="respond_content" class="font_1"><?php echo strlen($detail['content']); ?></span>)<br />
                            <textarea name="content" style="width:100%;height:130px;" onkeyup="record_charlen(this,<?=$MOD['respond_max']?>,'respond_content');"><?=$detail['content']?></textarea>
                        </td>
                    </tr>
                </table>
                <div class="text_center">
                    <? if($op=='edit') { ?>
                    <input type="hidden" name="edit" value="yes" />
                    <input type="hidden" name="respondid" value="<?=$respondid?>" />
                    
<? } else { ?>
                    <input type="hidden" name="rid" value="<?=$rid?>" />
                    <? } ?>
                    <input type="hidden" name="do" value="<?=$op?>" />
                    <input type="hidden" name="forward" value="<?php echo get_forward(); ?>" />
                    <? if(defined('IN_AJAX')) { ?>
                    <button type="button" onclick="ajaxPost('frm_respond','<?=$myid?>','get_respond');">提交</button>&nbsp;
                    <button type="button" onclick="dlgClose();">关闭</button>
                    
<? } else { ?>
                    <button type="submit" name="dosubmit" value="yes"> 提交 </button>&nbsp;
                    <button type="button" onclick="history.go(-1);"> 返回 </button>&nbsp;
                    <? } ?>
                </div>
                </form>
            </div><? if(!defined('IN_AJAX')) { ?>
        </div>
    </div>
    <div class="clear"></div>
</div>
<div class="mybottom"></div>
</div><?php footer(); } ?>
