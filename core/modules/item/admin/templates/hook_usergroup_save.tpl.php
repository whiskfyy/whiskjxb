<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<tr>
    <td class="altbg1"><strong>限制本组会员添加主题数量:</strong>限制用户添加主题的数量，留空或者为0表示不限制，-1为不允许添加。</td>
    <td><?=form_input("access[item_subjects]",$access['item_subjects'],'txtbox4')?></td>
</tr>
<tr>
    <td class="altbg1"><strong>限制本组会员图片上传数量:</strong>限制用户上传主题图片的数量，留空或者为0表示不限制，-1为不允许上传。</td>
    <td><?=form_input("access[item_pictures]",$access['item_pictures'],'txtbox4')?></td>
</tr>