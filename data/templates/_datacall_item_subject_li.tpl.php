<? !defined('IN_MUDDER') && exit('Access Denied'); ?><?php $reviewcfg = $_G['loader']->variable('config','review'); ?><ul class="rail-list">
<? if(is_array($_QUERY['mydata'])) { foreach($_QUERY['mydata'] as $key => $val) { ?>
<li><cite class="start<?php echo get_star($val['avgsort'], $reviewcfg['scoretype']); ?>" style="height:16px;"></cite><a href="<?php echo url("item/detail/id/{$val['sid']}"); ?>"><?php echo trimmed_title($val['name'].$val['subname'],10); ?></a></li>
<? } } ?>
</ul>