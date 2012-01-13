<? !defined('IN_MUDDER') && exit('Access Denied'); include template('modoer_header'); ?>
<script type="text/javascript">
function select_search(x) {
    $('#search_result').css('display','').html(x);
    $("#search_result li").each(function(i){ 
        $(this).mouseover(function(){$(this).css("background","#C1EBFF")})
            .mouseout(function(){$(this).css("background","#FFF")})
            .click(function(){location.href=Url("item/member/ac/<?=$ac?>/sid/"+$(this).attr('sid'))});
    });
    $('#search_result').append('<div><a href="<?php echo url("item/member/ac/subject_add"); ?>"><span class="font_1">以上都不是，我要添加新主题</span></a></div>');
}

$(document).ready(function() {
    if($('#keyword')[0] != null) {
        $('#keyword').keydown(function(e) {
            keynum = window.event ? e.keyCode : e.which;
            if(keynum == 13) search_subject('','keyword', select_search);
        });
    }
});
</script>
<div id="body">
<div class="myhead"></div>
<div class="mymiddle">
    <div class="myleft">
        
<? include template('menu','member','member'); ?>
    </div>
    <div class="myright">
        <div class="myright_top"></div>
        <div class="myright_middle">
            <h3>上传图片</h3>
            <div class="mainrail">
            <? if(!$sid) { ?>
                <table width="100%" cellspacing="0" cellpadding="0" class="maintable">
                    <tr>
                        <td width="100" align="right">主题名称：</td>
                        <td width="*">
                            <input type="text" name="keyword" id="keyword" class="t_input" size="30" />&nbsp;
                            <span class="font_3">例如：必胜客</font>&nbsp;
                            <button type="button" onclick="search_subject('','keyword',select_search);">搜索</button>&nbsp;
                            <button type="button" onclick="search_myreviewed(select_search);">我点评的主题</button>&nbsp;
                            <button type="button" onclick="search_myfavorite(select_search);">我的收藏</button>&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><div id="search_result" style="display:none;"></div></td>
                    </tr>
                </table>
            
<? } elseif($_GET['op'] == 'multi') { ?>
                <table width="100%" cellspacing="0" cellpadding="0" class="maintable">
                    <tr>
                        <td width="*">上传对象：<a href="<?php echo url("item/detail/id/{$subject['sid']}"); ?>" target="_blank"><?=$subject['name']?>&nbsp;<?=$subject['subname']?></a></td>
                    </tr>
                </table>
                <div id="upload">
                    <div id="up_1">
                        <form method="post" name="myform[]" action="<?php echo url("item/member/ac/{$ac}/in_ajax/1"); ?>" target="iframe_upload" enctype="multipart/form-data">
                            <input type="hidden" name="multi" value="yes" />
                            <input type="hidden" name="sid" value="<?=$sid?>" />
                            <input type="hidden" name="dosubmit" value="yes" />
                            <div style="margin:5px;">选择图片：<input type="file" name="picture" size="30"></div>
                        </form>
                    </div>
                </div>
                <div>
                    <input type="hidden" id="sid" value="<?=$sid?>" />
                    <input type="hidden" id="forward" value="<?php echo get_forward(); ?>" />
                    <button type="button" onclick="pic_upload_start();">开始上传</button>&nbsp;
                    <button type="button" onclick="location='<?php echo url("item/member/ac/{$ac}/sid/{$sid}"); ?>'">返回单个上传</button>&nbsp;
                </div>
                <iframe name="iframe_upload" id="iframe_upload" style="display:none;"></iframe>
                <script type="text/javascript">
                var num = <?=$multi_num?>;
                var index = 1;
                var errcount = 0;
                $(document).ready(function() {
                    for (var i=2; i<=num; i++) {
                        $('#upload').append('<div id="up_'+i+'">'+$('#up_1').html()+'</div>');
                    }
                });
                function pic_upload_start() {
                    $('#iframe_upload').load(pic_upload);
                    $('#up_1 form').submit();
                }
                function pic_upload() {
                    var iframe = document.getElementById('iframe_upload');
                    var data = $(iframe.contentWindow.document.body).html();
                    if(data.match(/\{\s+caption:".*",message:".*".*\s*\}/)) {
                        var mymsg = eval('('+data+')');
                        if(data.indexOf('ERROR:') > 1) {
                            errcount++;
                            mymsg.message = mymsg.message.replace('ERROR:','');
                        } else {
                            $('#up_'+index).html($('#up_'+index+' [@name=picture]').val());
                        }
                        $('#up_'+index).append(mymsg.message);
                    } else {
                        $('#up_'+index).append(data);
                    }
                    index++;
                    if(index <= num) {
                        $('#up_'+index+' form').submit();
                    } else {
                        if(confirm(num - errcount + '张图片被上传成功，是否继续上传？')) {
                            document.location.href = "<?php echo url("item/member/ac/pic_upload/op/multi/sid/{$sid}"); ?>";
                        } else {
                            document.location.href = "<?php echo url("item/member/ac/m_picture"); ?>";
                            //document.location.href = $('#forward').val();
                        }
                    }
                }
                </script>
            
<? } else { ?>
                <form method="post" name="myform" action="<?php echo url("item/member/ac/{$ac}/rand/{$_G['random']}"); ?>" enctype="multipart/form-data">
                <table width="100%" cellspacing="0" cellpadding="0" class="maintable">
                    <tr>
                        <td width="100" align="right"><?=$model['item_name']?>名称：</td>
                        <td width="*"><a href="<?php echo url("item/detail/id/{$subject['sid']}"); ?>" target="_blank"><?=$subject['name']?>&nbsp;<?=$subject['subname']?></a></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right" valign="top"><span class="font_1">*</span>选择图片：</td>
                        <td><input type="file" name="picture" class="f_input"><br /><span class="font_3">最大允许上传的图片大小<span class="font_2"><?=$_CFG['picture_upload_size']?>KB</span>，允许上传的图片格式 jpg、png、gif</span></td>
                    </tr>
                    <tr>
                        <td align="right"><span class="font_1">*</span>图片标题：</td>
                        <td><input type="text" name="title" size="35" class="t_input">&nbsp;<span class="font_3">&nbsp;限制<span class="font_2">20</span>个字符</span></td>
                    </tr>
                    <tr>
                        <td align="right">图片说明：</td>
                        <td><input type="text" name="comments" size="45" class="t_input">&nbsp;<span class="font_3">&nbsp;限制<span class="font_2">60</span>个字符</span></td>
                    </tr>
                </table>
                <div class="text_center">
                    <input type="hidden" name="sid" value="<?=$sid?>" />
                    <input type="hidden" name="forward" value="<?php echo get_forward(); ?>" />
                    <button type="submit" name="dosubmit" value="yes">开始上传</button>&nbsp;
                    <? if($MOD['multi_upload_pic']) { ?>
                    <button type="button" onclick="location='<?php echo url("item/member/ac/{$ac}/op/multi/sid/{$sid}"); ?>'">批量上传</button>&nbsp;
                    <? } ?>
                    <button type="button" onclick="history.go(-1);">返回</button>
                </div>
                </form>
            <? } ?>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<div class="mybottom"></div>
</div><?php footer(); ?>