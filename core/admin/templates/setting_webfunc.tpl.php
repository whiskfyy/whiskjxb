<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<div id="body">
<?=form_begin(cpurl($module,$act,$op))?>
    <div class="space">
        <div class="subtitle">网站功能</div>
        <ul class="cptab">
            <li class="selected" id="btn_config1"><a href="#" onclick="tabSelect(1,'config');" onfocus="this.blur()">主要功能</a></li>
            <li id="btn_config2"><a href="#" onclick="tabSelect(2,'config');" onfocus="this.blur()">图片上传</a></li>
            <li id="btn_config3"><a href="#" onclick="tabSelect(3,'config');" onfocus="this.blur()">JS调用</a></li>
        </ul>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0" id="config1">
            <tr>
                <td width="45%" class="altbg1"><strong>页面 Gzip 压缩:</strong>将页面内容以 gzip 压缩后传输，可以加快传输速度，需 PHP 4.0.4 以上且支持 Zlib 模块才能使用。</td>
                <td width="*"><?=form_bool('setting[gzipcompress]', $config['gzipcompress'])?></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>显示页面信息:</strong>在页面底部，显示数据库查询次数和页面执行时间。<br /><span class="font_1">一部分页面启用了"整页缓存"功能时，本功能将失效。</span></td>
                <td><?=form_bool('setting[scriptinfo]', $config['scriptinfo'])?></td>
            </tr>
			<tr>
                <td class="altbg1"><strong>模板后缀:</strong>自定义后缀名，可防模板名被猜测，默认为.htm，必须加上后缀点号。<br /><span class="font_1">请确保模板目录下的各个文件后缀与此处设置保持一致。</font></td>
                <td><input type="text" name="setting[tplext]" value="<?=$config['tplext']?>" class="txtbox4" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>地图API的地址:</strong>填写根据不同的地图商提供的API功能的JS地址。</td>
                <td><input type="text" name="setting[mapapi]" value="<?=$config['mapapi']?>" class="txtbox" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>地图标识:</strong>根据不同的地图标识，系统会载入不同的地图js（前提是地图js存在），默认是表示为51ditu</td>
                <td><input type="text" name="setting[mapflag]" value="<?=$config['mapflag']?>" class="txtbox3" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>地图API的编码:</strong>默认地图不需要设置编码，Google地图需要设置为UTF-8</td>
                <td><input type="text" name="setting[mapapi_charset]" value="<?=$config['mapapi_charset']?>" class="txtbox4" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>地图默认缩放等级:</strong>不同的地图有自己的缩放等级，一般在1-15之前，可自行填写一个数字后前台测试，设置自己满意的值，只能填写数字</td>
                <td><input type="text" name="setting[map_view_level]" value="<?=$config['map_view_level']?>" class="txtbox5" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>编辑器上传附件使用相对路径:</strong>在WEB编辑器中，使用上传图片，文件等上传功能时，在上传后使用相对路径来显示图片，文件等。相对路径的好处在于可以针对更改域名时，上传的图片和文件会自动指向新域名，不使用相对路径时，文章里的图片等上传文件将失效，因为全局路径会直接写死域名。</td>
                <td><?=form_bool('setting[editor_relativeurl]', $config['editor_relativeurl'])?><div><span class="font_2">如果你的网站是一级域名或者二级域名(例如:http://demo.modoer.com)，建议打开；如果你的网站是放在二级目录里(例如:http://www.modoer.com/modoer)，请关闭本功能，因为会影响正常网站的图片等上传文件的显示。</span></div></td>
            </tr>
        </table>

        <table class="maintable" border="0" cellspacing="0" cellpadding="0" id="config2" style="display:none;">
            <tr>
                <td class="altbg1" width="45%"><strong>默认上传尺寸限制:</strong>系统默认图片最大上传尺寸，单位：KB</td>
                <td width="*"><input type="text" name="setting[picture_upload_size]" value="<?=$config['picture_upload_size']?>" class="txtbox4" /> KB</td>
            </tr>
            <tr>
                <td class="altbg1"><strong>默认允许图片类型:</strong>不同类型请用【空格】分割，默认为：jpg jpeg png gif</td>
                <td><input type="text" name="setting[picture_ext]" value="<?=$config['picture_ext']?>" class="txtbox" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>默认图片水印功能:</strong>在默认上传图片时，给图片加上水印，支持PNG类型水印，水印图片存放在./static/images/watermark.png，您可替换水印文件以实现不同的水印效果，水印功能需要GD库支持。</td>
                <td><?=form_bool('setting[watermark]',$config['watermark'])?></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>默认水印位置:</strong>设置水印在图片上的位置，默认是在右下角</td>
                <td><?=form_radio('setting[watermark_postion]', array(0=>'随机',1=>'左上角',2=>'右上角',3=>'左下角',4=>'右下角',5=>'居中'), $config['watermark_postion'],'','&nbsp;')?></td>
            </tr>
            <tr>
                <td class="altbg1" width="45%"><strong>缩略图生成质量:</strong>设置缩略图的生成质量，值越大，质量越高，同时占用空间也大，默认是80，最大100；<span class="font_1">不建议设置为100，可能缩略图的文件大小会大于原图。</span></td>
                <td width="*"><input type="text" name="setting[picture_createthumb_level]" value="<?=$config['picture_createthumb_level']?>" class="txtbox4" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>缩略图生成模式:</strong>按尺寸裁剪：对图片缩小并裁剪，超出部分裁剪，确保图片大小与设置的高宽相等；等比例缩小：宽和高只取其中1项，宽优先选择。建议(默认)选择按尺寸裁剪。</td>
                <td><?=form_radio('setting[picture_createthumb_mod]', array('按尺寸裁剪','等比例缩小'), $config['picture_createthumb_mod'],'','&nbsp;')?></td>
            </tr>
        </table>

        <table class="maintable" border="0" cellspacing="0" cellpadding="0" id="config3" style="display:none;">
            <tr>
                <td class="altbg1" class="altbg1" width="45%"><strong>开启JS调用功能:</strong>开启系统的JS远程调用功能。</td>
                <td width="*"><?=form_bool('setting[jstransfer]', $config['jstransfer'])?></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>JS来路限制:</strong>只允许列表中的域名才可以使用JS调用功能，每个域名一行，请勿包含 http:// 或其他非域名内容，留空为不限制来路，即任何网站均可调用.但是多网站调用会加重您的服务器负担。</td>
                <td><textarea name="setting[jsaccess]" rows="6" cols="40" class="txtarea"><?=$config['jsaccess']?></textarea></td>
            </tr>
        </table>
    </div>
    <center><?=form_submit('dosubmit',lang('admincp_submit'),'yes','btn')?></center>
<?=form_end()?>
</div>