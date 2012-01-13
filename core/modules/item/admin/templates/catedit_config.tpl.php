<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<div id="body">
<form method="post" action="<?=cpurl($module,$act,$op)?>">
    <div class="space">
        <div class="subtitle">分类管理</div>
        <ul class="cptab">
            <li class="selected"><a href="<?=cpurl($module,$act,'config',array('catid'=>$catid))?>" onfocus="this.blur()">参数设置</a></li>
            <li><a href="<?=cpurl($module,$act,'subcat',array('catid'=>$catid))?>" onfocus="this.blur()">子分类管理</a></li>
        </ul>
        <table class="maintable" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="45%" class="altbg1"><strong>分类名称：</strong></td>
                <td width="*"><input type="text" name="t_cat[name]" class="txtbox3" value="<?=$t_cat['name']?>" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>分类模型：</strong></td>
                <td><select name="t_cat[modelid]" disabled="disabled">
                    <option value="">==模型列表==</option>
					<?=form_item_models($t_cat['modelid'])?>
                    </select>&nbsp;
                    <a href="<?=cpurl($module,'model_edit','',array('modelid'=>$t_cat['modelid']))?>">编辑模型</a>&nbsp;
                    <a href="<?=cpurl($module,'field_list','',array('modelid'=>$t_cat['modelid']))?>">字段管理</a>
                </td>
            </tr>
            <tr><td colspan="2" class="altbg2"><center><strong>功能设置</strong></center></td></tr>
            <tr>
                <td class="altbg1"><strong>启用<?=$t_mod['item_name']?>留言功能：</strong>会员可对正在访问的某<?=$t_mod['item_unit']?><?=$t_mod['item_name']?>进行留言，<?=$t_mod['item_name']?>管理员可回复这些留言。</td>
                <td><?=form_bool('t_cfg[gusetbook]', $t_cfg['gusetbook'])?></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>启用<?=$t_mod['item_name']?>管理认领功能：</strong><?=$t_mod['item_name']?>被领用后，领用人可编辑管理<?=$t_mod['item_name']?>的信息。</td>
                <td><?=form_bool('t_cfg[subject_apply]', $t_cfg['subject_apply'])?></td>
            </tr>
            <tr>
                <td class="altbg1"><div style="margin-left:20px;"><strong>管理认领图片上传：</strong>在开启认领功能后，是否需要图片上传功能，用于上传照片或者企业营业执照。</div></td>
                <td><?=form_bool('t_cfg[subject_apply_uppic]', $t_cfg['subject_apply_uppic'])?></td>
            </tr>
            <tr>
                <td class="altbg1"><div style="margin-left:40px;"><strong>图片上传的名称：</strong>给图片的用途取名，例如营业执照。</div></td>
                <td><input type="text" name="t_cfg[subject_apply_uppic_name]" value="<?=$t_cfg['subject_apply_uppic_name']?>" class="txtbox3" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>启用会员参与功能：</strong>开启本功能后，可对主题进行对应的投票行为，例如去过，想去等。</td>
                <td><?=form_bool('t_cfg[useeffect]', $t_cfg['useeffect'])?></td>
            </tr>
            <tr>
                <td class="altbg1"><div style="margin-left:20px;"><strong>会员参与名词：</strong>设置会员的行为名词，例如：去过，想去；目前支持2种行为</div></td>
                <td>
                    <input type="text" name="t_cfg[effect1]" value="<?=$t_cfg['effect1']?>" class="txtbox4" />，
                    <input type="text" name="t_cfg[effect2]" value="<?=$t_cfg['effect2']?>" class="txtbox4" /><br />
                </td>
            </tr>
            <tr>
                <td class="altbg1"><strong>允许添加<?=$t_mod['item_name']?>分店：</strong>用户可向已存在的<?=$t_mod['item_name']?>增加分店。</td>
                <td><?=form_bool('t_cfg[use_subbranch]', $t_cfg['use_subbranch'])?></td>
            </tr>
            <?if($attcats = $_G['loader']->variable('att_cat','item',false)):?>
            <tr>
                <td class="altbg1" valign="top"><strong>启用属性组筛选：</strong>在主题列表页可进行属性组筛选</td>
                <td>
					<?foreach($attcats as $key => $val):?>
                    <input type="checkbox" name="t_cfg[attcat][]" id="attcat_<?=$key?>" value="<?=$val['catid']?>"<?if($t_cfg['attcat'] && in_array($val['catid'], $t_cfg['attcat']))echo' checked';?> />&nbsp;<label for="attcat_<?=$key?>"><?=$val['name']?>&nbsp;(<?=$val['des']?>)</label><br />
                    <?endforeach;?>
                </td>
            </tr>
            <?endif;?>
            <tr><td colspan="2" class="altbg2"><center><strong>点评功能设置</strong></center></td></tr>
            <tr>
                <td class="altbg1"><strong>选择点评项组：</strong>指定一个点评项，用于会员对主题的打分依据。</td>
                <td><select name="t_cat[review_opt_gid]">
                    <option value="">==点评项组列表==</option>
					<?=form_review_opt_group($t_cat['review_opt_gid'])?>
                </select>
            <tr>
                <td class="altbg1" valign="top"><strong>启用点评标签：</strong>设置会员在点评时允许填写的标签组</td>
                <td>
                    <?foreach($t_tag as $key => $val):?>
                    <input type="checkbox" name="t_cfg[taggroup][]" id="taggroup_<?=$key?>" value="<?=$val['tgid']?>"<?if($t_cfg['taggroup'] && in_array($val['tgid'], $t_cfg['taggroup']))echo' checked';?> />&nbsp;<label for="taggroup_<?=$key?>"><?=$val['name']?>&nbsp;(<?=$val['des']?>)</label><br />
                    <?endforeach;?>
                </td>
            </tr>
            <tr>
                <td class="altbg1"><strong>启用点评价格字段：</strong>使用价格功能后，在点评时将显示价格输入框，用于填写消费价格。</td>
                <td><?=form_bool('t_cfg[useprice]', $t_cfg['useprice'])?></td>
            </tr>
            <tr>
                <td class="altbg1"><div style="margin-left:20px;"><strong>价格字段是否必填：</strong>本功能在启用价格字段时有效。</div></td>
                <td><?=form_bool('t_cfg[useprice_required]', $t_cfg['useprice_required'])?></td>
            </tr>
            <tr>
                <td class="altbg1"><div style="margin-left:20px;"><strong>价格字段的显示名称：</strong>显示在点评页面中。本功能在启用价格字段时有效。</div></td>
                <td><input type="text" name="t_cfg[useprice_title]" value="<?=$t_cfg['useprice_title']?>" class="txtbox4" /></td>
            </tr>
            <tr>
                <td class="altbg1"><div style="margin-left:20px;"><strong>价格字段的单位：</strong>显示在点评页面中。例如：元/人。本功能在启用价格字段时有效。</div></td>
                <td><input type="text" name="t_cfg[useprice_unit]" value="<?=$t_cfg['useprice_unit']?>" class="txtbox4" /></td>
            </tr>
            <tr>
                <td class="altbg1" valign="top"><strong>允许重复点评：</strong>允许会员对本分类下的<?=$t_mod['item_name']?>重复点评</td>
                <td><?=form_bool('t_cfg[repeat_review]', $t_cfg['repeat_review'])?></td>
            </tr>
            <tr>
                <td class="altbg1" valign="top"><div style="margin-left:20px;"><strong>重复点评数量限制：</strong>本功能在开启允许重复点评后生效。限制会员重复点评某一<?=$t_mod['item_unit'].$t_mod['item_name']?>的数量，0为无限制，默认为无限制</div></td>
                <td><?=form_input('t_cfg[repeat_review_num]', (int)$t_cfg['repeat_review_num'], 'txtbox4')?>&nbsp;次</td>
            </tr>
            <tr>
                <td class="altbg1" valign="top"><div style="margin-left:20px;"><strong>重复点评时间间隔：</strong>本功能在开启允许重复点评后生效。限制2次点评之间的时间间隔，0为无限制，默认为无限制</div></td>
                <td><?=form_input('t_cfg[repeat_review_time]', (int)$t_cfg['repeat_review_time'], 'txtbox4')?>&nbsp;分钟</td>
            </tr>
            <tr>
                <td class="altbg1"><strong>允许游客点评：</strong>开启游客点评后，系统将没有登录点评限制，游客点评可能引起恶意刷分，无法记录会员信息的问题，请谨慎使用。</td>
                <td><?=form_bool('t_cfg[guest_review]', $t_cfg['guest_review'])?></td>
            </tr>
            <tr><td colspan="2" class="altbg2"><center><strong>审核设置</strong></center></td></tr>
            <tr>
                <td class="altbg1" valign="top"><strong>相关审核：</strong>开启审核功能后，未审核的信息将暂时不在前台显示和操作。</td>
                <td style="line-height:20px;">
					<div>添加<?=$t_mod['item_name']?>：<?=form_bool('t_cfg[itemcheck]', $t_cfg['itemcheck'])?></div>
					<div>点评内容：<?=form_bool('t_cfg[reviewcheck]', $t_cfg['reviewcheck'])?></div>
					<div>上传图片：<?=form_bool('t_cfg[picturecheck]', $t_cfg['picturecheck'])?></div>
                    <div>发表留言：<?=form_bool('t_cfg[guestbookcheck]', $t_cfg['guestbookcheck'])?></div>
				</td>
            </tr>
            <tr class="altbg2"><td colspan="2"><center><strong>界面设置</strong></center></td></tr>
            <tr>
                <td class="altbg1"><strong>内容页默认风格:</strong>内容页风格，默认不使用风格，与主站统一风格。</td>
                <td><select name="t_cfg[templateid]">
                    <option value="0">不使用风格</option>
					<?=form_template('item', $t_cfg['templateid'])?>
                </select></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>列表页默认显示:</strong>在分类列表页内默认显示的模式。</td>
                <td><?=form_select('t_cfg[displaytype]', lang('item_list_displytype'), $t_cfg['displaytype'])?></td>
            </tr>
            <tr>
                <td valign="top" class="altbg1"><strong>列表页默认排序：</strong>在分类列表页内默认使用的排序方法。</td>
                <td><?=form_select('t_cfg[listorder]', lang('item_list_orderlist'), $t_cfg['listorder'])?></td>
            </tr>
            <?if($_G['modules']['product']):?>
            <tr class="altbg2"><td colspan="2"><center><strong>主题产品</strong></center></td></tr>
            <tr>
                <td class="altbg1"><strong>管理产品模型：</strong>选择本分类的主题相关联的产品类型。</td>
                <?php $_G['loader']->helper('form','product');?>
                <td><select name="t_cfg[product_modelid]">
                    <option value="0" selected="selected">选择产品模型</option>
                    <?=form_product_model($t_cfg['product_modelid']);?>
                </select>
                </td>
            </tr>
            <?endif;?>
            <tr class="altbg2"><td colspan="2"><center><strong>搜索引擎优化</strong></center></td></tr>
            <tr>
                <td class="altbg1"><strong>Meta Keywords：</strong>Keywords 项出现在页面头部的 Meta 标签中，用于记录本页面的关键字，多个关键字间请用半角逗号 "," 隔开。</td>
                <td><input type="text" name="t_cfg[meta_keywords]" value="<?=$t_cfg['meta_keywords']?>" class="txtbox" /></td>
            </tr>
            <tr>
                <td class="altbg1"><strong>Meta Description：</strong>Description 出现在页面头部的 Meta 标签中，用于记录本页面的概要与描述。</td>
                <td><input type="text" name="t_cfg[meta_description]" value="<?=$t_cfg['meta_description']?>" class="txtbox" /></td>
            </tr>
        </table>
        <center>
            <input type="hidden" name="catid" value="<?=$_GET['catid']?>" />
            <button type="submit" name="dosubmit" value="yes" class="btn" /><?=lang('global_submit')?></button>&nbsp;
            <button type="button" class="btn" onclick="location.href='<?=cpurl($module,'category_list')?>'" /><?=lang('global_return')?></button>
        </center>
    </div>
</form>
</div>