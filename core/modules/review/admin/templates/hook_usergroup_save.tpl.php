<?php (!defined('IN_ADMIN') || !defined('IN_MUDDER')) && exit('Access Denied'); ?>
<tr>
    <td class="altbg1"><strong>限制本组会员点评数量:</strong>限制用户发表点评的数量，留空或者为0表示不限制，-1为不允许发表。</td>
    <td><?=form_input("access[review_num]",$access['review_num'],'txtbox4')?></td>
</tr>
<tr>
    <td class="altbg1"><strong>限制本组会员重复点评:</strong>限制用户重复点评。</td>
    <td><?=form_bool("access[review_repeat]",$access['review_repeat'])?></td>
</tr>