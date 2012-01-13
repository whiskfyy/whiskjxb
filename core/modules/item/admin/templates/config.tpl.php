<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<div id="body">
<?=form_begin(cpurl($module,$act))?>
    <div class="space">
        <div class="subtitle"><?=$MOD['name']?>&nbsp;模块配置</div>
        <ul class="cptab">
            <li class="selected" id="btn_config1"><a href="#" onclick="tabSelect(1,'config');" onfocus="this.blur()">功能配置</a></li>
            <li id="btn_config2"><a href="#" onclick="tabSelect(2,'config');" onfocus="this.blur()">显示配置</a></li>
        </ul>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0" id="config1">
            <tr>
                <td width="45%" class="altbg1"><strong>默认主分类:</strong>在首页或未指名主分类的情况下，默认显示哪个分类的内容；没有可分类时，请再网站管理页面，增加点评主分类</td>
                <td width="*"><select name="modcfg[pid]">
                    <option value="">==选择主分类==</option>
					<?=form_item_category_main($modcfg['pid'])?>
                </select></td>
            </tr>
            <tr>
                <td class="altbg1" valign="top"><strong>启用主题二/三级域名或个性目录:</strong>本功能只能在二级，三级域名中使用，不能在二级目录里使用；同时您的服务器需要多域名的绑定（或泛解析）；</td>
                <td><?=form_radio('modcfg[sldomain]', array(0=>'关闭',1=>'主题二/三级域名',2=>'个性目录',3=>'二者都需要'), $modcfg['sldomain'])?><br /><span class="font_1">打开本功能后，请确定data/config.php文件里$_G['cookiedomain']的值为一级域名（不包括www)，例如modoer.com，否则会员登录会失败；启用个性目录需要开启目录形式的URL改写功能，同时要配置data/rewrite_pathinfo.inc文件，具体设置请到官网查看。</span></td>
            </tr>
            <tr>
                <td class="altbg1" valign="top"><div style="margin-left:20px;"><strong>主题二/三级域名基准:</strong>设置二/三级域名的基准域名，例如想实现shopname.abc.com的二级域名，则基准为abc.com，如果想实现三级域名shopname.shop.abc.com，则基准为shop.abc.com</div></td>
                <td><input type="text" name="modcfg[base_sldomain]" value="<?=$modcfg['base_sldomain']?>" class="txtbox3" /><br /><span class="font_1">本功能只在开启了主题二/三级域名有效，如果开启多城市二级域名功能，请关闭本功能</span></td>
            </tr>
            <tr>
                <td class="altbg1" valign="top"><div style="margin-left:20px;"><strong>主题二/三级域名/个性目录保留项:</strong>可以设置一些预留的名称，以免自己在今后需要使用时候造成访问冲突等问题,多个请用逗号","分隔，系统会禁止使用模块标签作为域名/个性目录名称。</div></td>
                <td><input type="text" name="modcfg[reserve_sldomain]" value="<?=$modcfg['reserve_sldomain']?>" class="txtbox" /></td>
            </tr>
            <tr>
                <td class="altbg1" valign="top"><strong>表单验证码:</strong>开启验证码可减少广告机提交信息，但是也会让会员感到繁琐</td>
                <td>
                    <div>发布主题:<?=form_bool('modcfg[seccode_subject]', $modcfg['seccode_subject'])?></div>
                    <div>发布点评(会员):<?=form_bool('modcfg[seccode_review]', $modcfg['seccode_review'])?></div>
                    <div>发布点评(游客):<?=form_bool('modcfg[seccode_review_guest]', $modcfg['seccode_review_guest'])?></div>
                    <div>发布留言:<?=form_bool('modcfg[seccode_guestbook]', $modcfg['seccode_guestbook'])?></div>
                </td>
            </tr>
            <tr>
                <td class="altbg1"><strong>启用批量上传图片</strong>打开批量上传后，会员可以一次性最多上传20张图片</td>
                <td><?=form_bool('modcfg[multi_upload_pic]',$modcfg['multi_upload_pic']);?></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>批量上传图片限制</strong>一次提交最多上传多少张图片，至多20张，至少2张，默认5张</td>
                <td><input type="text" name="modcfg[multi_upload_pic_num]" value="<?=$modcfg['multi_upload_pic_num']?>" class="txtbox5" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>点评内容字数限制 </strong>定义点评内容的字符限制</td>
                <td><input type="text" name="modcfg[review_min]" value="<?=$modcfg['review_min']?>" class="txtbox5" /> - <input type="text" name="modcfg[review_max]" value="<?=$modcfg['review_max']?>" class="txtbox5" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>留言内容字数限制 </strong>定义留言内容的字符限制</td>
                <td><input type="text" name="modcfg[guestbook_min]" value="<?=$modcfg['guestbook_min']?>" class="txtbox5" /> - <input type="text" name="modcfg[guestbook_max]" value="<?=$modcfg['guestbook_max']?>" class="txtbox5" /></td>
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
            <tr>
                <td class="altbg1">
                    <strong>关闭搜索独条结果的跳转功能</strong>当使用搜索主题功能时，如遇到只有1条结果时，程序自动会跳转到该条主题内容页，如果设置关闭，程序将不再跳转。
                </td>
                <td><?=form_bool('modcfg[search_location]', $modcfg['search_location'])?></td>
            </tr>
        </table>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0" id="config2" style="display:none;">
            <tr>
                <td width="45%" class="altbg1"><strong>图片尺寸:</strong>上传点评对象的图片时，限制期最大尺寸，格式为：宽 x 高；默认：200 x 150</td>
                <td width="*"><input type="text" name="modcfg[pic_width]" value="<?=$modcfg['pic_width']?>" class="txtbox5" />&nbsp;x&nbsp;<input type="text" name="modcfg[pic_height]" value="<?=$modcfg['pic_height']?>" class="txtbox5" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>视频播放器尺寸:</strong>在商铺页面显示视频的尺寸，格式为：宽 x 高；默认：250 x 200</td>
                <td><input type="text" name="modcfg[video_width]" value="<?=$modcfg['video_width']?>" class="txtbox5" />&nbsp;x&nbsp;<input type="text" name="modcfg[video_height]" value="<?=$modcfg['video_height']?>" class="txtbox5" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>列表页显示主题量:</strong>列表页，搜索页面中每页显示点评对象数量</td>
                <td><?=form_radio('modcfg[list_num]',array('10'=>'10条','20'=>'20条','40'=>'40条'),$modcfg['list_num'])?></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>内容页面显示点评数:</strong>内容页面中每页显示点评数目</td>
                <td><?=form_radio('modcfg[review_num]',array('5'=>'5条','10'=>'10条','20'=>'20条'),$modcfg['review_num'])?></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>分类排序:</strong>调整分类排列顺序方式</td>
                <td><?=form_radio('modcfg[classorder]',array('total'=>'按分类中的数量','order'=>'按分类顺序'),$modcfg['classorder'])?></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>主题封面设置:</strong>点评对象的封面显示</td>
                <td><?=form_select('modcfg[thumb]',array('1'=>'最新上传的图片','2'=>'自适应(无图时选最新)','3'=>'手动选择图片'),$modcfg['thumb'])?></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>内容页显示缩略图:</strong>在内容页面显示主题的缩略图列表</td>
                <td><?=form_bool('modcfg[show_thumb]', $modcfg['show_thumb'])?></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>关闭内容页的数据统计功能:</strong>在主题内容页面，会显示当前主题的具体一些统计信息，使用功能后将关闭显示统计信息。</td>
                <td><?=form_bool('modcfg[close_detail_total]', $modcfg['close_detail_total'])?></td>
            </tr>

        </table>
    </div>
    <center><?=form_submit('dosubmit',lang('admincp_submit'),'yes','btn')?></center>
<?=form_end()?>
</div>