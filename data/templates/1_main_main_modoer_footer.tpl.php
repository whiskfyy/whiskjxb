<? !defined('IN_MUDDER') && exit('Access Denied'); ?>
<?="\r\n"?>
<div id="footer">
    <?php $foot_menus = $_CFG['foot_menuid'] ? $_G['loader']->variable('menu_' . $_CFG['foot_menuid']) : ''; ?>    <div class="links">
        
<? if(is_array($foot_menus)) { foreach($foot_menus as $val) { ?>
        <a href="<?php echo url("{$val['url']}"); ?>"<? if($val['target']) { ?>
 target="<?=$val['target']?>"<? } ?>
><?=$val['title']?></a>&nbsp;|
        
<? } } ?>
        <a href="javascript://;" onclick="window.scrollTo(0,0);">TOP</a>
    </div>
    <div>
         Powered by <span class="product"><a href="http://www.modoer.com/" target="_blank">Modoer</a></span> <span class="version"><?=$_G['modoer']['version']?><? if($_G['modoer']['beta']) { ?>
 Beta <?=$_G['modoer']['beta']?><? } if($_CFG['scriptinfo']) { ?>
 Build <?=$_G['modoer']['build']?><? } ?>
</span> &copy; 2007-2010 <a href="http://www.moufer.cn/" target="_blank">Moufer Studio</a><br />免责声明：站内会员言论仅代表个人观点，并不代表本站同意其观点，本站不承担由此引起的法律责任。
    </div>
    <div class="bottom">
        <? if($_CFG['icpno']) { ?>
<a href="http://www.miibeian.gov.cn/" target="_blank"><?=$_CFG['icpno']?></a><? } ?>
<?=$sitedebug?>
    </div>
    <script src="http://s16.cnzz.com/stat.php?id=3756131&web_id=3756131&show=pic" language="JavaScript"></script>
</div><? if(DEBUG) { ?>
<?php echo $_G['db']->debug_print();; ?><?php echo $_G['loader']->debug_print();; } ?>
</body>
</html>