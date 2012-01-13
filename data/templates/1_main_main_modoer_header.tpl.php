<? !defined('IN_MUDDER') && exit('Access Denied'); ?><?php if(SCRIPTNAV != 'index'):
        $_HEAD['title'] .= $_G['cfg']['titlesplit'] . $_G['cfg']['sitename'];
    endif;
    if(!$_HEAD['keywords']):
        $_HEAD['keywords'] = $_G['cfg']['meta_keywords'];
    endif;
    if(!$_HEAD['description']):
        $_HEAD['description'] = $_G['cfg']['meta_description'];
    endif;
 ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$_G['charset']?>" />
<meta http-equiv="x-ua-compatible" content="ie=7" />
<title><?=$_HEAD['title']?> - Powered by Modoer</title>
<meta name="keywords" content="<?=$_HEAD['keywords']?>,modoer,mudder,点评系统,评论系统,商家点评,产品点评" />
<meta name="description" content="<?=$_HEAD['description']?>,免费开源的php点评系统modoer" />
<link rel="stylesheet" type="text/css" href="<?=URLROOT?>/<?=$_G['tplurl']?>css_common.css" />
<script type="text/javascript" src="<?=URLROOT?>/data/cachefiles/config.js"></script>
<script type="text/javascript"><? if(!empty($MOD)) { ?>
var mod = modules['<?=$MOD['flag']?>'];<? } ?>
</script>
<script type="text/javascript" src="<?=URLROOT?>/static/javascript/jquery.js"></script>
<script type="text/javascript" src="<?=URLROOT?>/static/javascript/common.js"></script><? if($_CFG['headhtml']) { ?>
<?=$_CFG['headhtml']?><? } ?>
</head>
<body><? if(SCRIPTNAV == 'index') { ?>
<style type="text/css">@import url("<?=URLROOT?>/<?=$_G['tplurl']?>css_index.css");</style>
<? } elseif(SCRIPTNAV == 'assistant') { ?>
<style type="text/css">@import url("<?=URLROOT?>/<?=$_G['tplurl']?>css_assistant.css");</style>
<? } elseif(!empty($MOD) && is_file(MUDDER_ROOT . $_G['tplurl'] . 'css_' . $MOD['flag'] . '.css')) { ?>
<style type="text/css">@import url("<?=URLROOT?>/<?=$_G['tplurl']?>css_<?=$MOD['flag']?>.css");</style><? } ?>
<script type="text/javascript" src="<?=URLROOT?>/static/javascript/member.js"></script><? if(!empty($MOD) && $MOD['flag']!='member' && is_file(MUDDER_ROOT . 'static/javascript/' . $MOD['flag'] . '.js')) { ?>
<script type="text/javascript" src="<?=URLROOT?>/static/javascript/<?=$MOD['flag']?>.js"></script><? } ?>
<div id="header">
    <div class="mainmenu">
        <div class="logo">
            <a href="<?php echo url("modoer/index"); ?>"><img src="<?=URLROOT?>/static/images/logo.jpg" alt="<?=$_CFG['sitename']?>" title="<?=$_CFG['sitename']?>"></a>
        </div>
        <div class="charmenu">
            <div id="login_0">
                <span class="arrow-ico"><a href="<?php echo url("member/login"); ?>">登录</a></span>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo url("member/reg"); ?>">快速注册</a>
            </div>
            <div id="login_1" style="display:none;">
                你好，<a href="<?php echo url("member/index"); ?>" id="login_name"></a>[<a href="<?php echo url("member/login/op/logout"); ?>">退出</a>]&nbsp;&nbsp;|&nbsp;&nbsp;<a 
                href="<?php echo url("member/index/ac/pm/folder/inbox"); ?>">短信箱</a><span id="login_newmsg" style="display:none;">(0)</span>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo url("space"); ?>">个人空间</a>
            </div>
            <div id="login_2" style="display:none;">
                <span id="login_activation"></span>,<a href="<?php echo url("ucenter/activation/auth/_activationauth_"); ?>" id="login_activation_a">您的帐号需要激活</a> [<a href="<?php echo url("member/login/op/logout"); ?>">退出</a>]
            </div>
            <div>
                <a href="<?php echo url("member/index"); ?>">我的助手</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo url("item/detail/random/1"); ?>">随便看看</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo url("item/member/ac/subject_add/pid/16"); ?>">新增保姆</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo url("review/member/ac/add"); ?>">我要点评</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo url("item/search"); ?>">搜索</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:;" onclick="this.style.behavior='url(#default#homepage)';this.setHomePage('<?=$_CFG['siteurl']?>')">设为首页</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:;" onclick="window.external.addFavorite('<?=$_CFG['siteurl']?>','<?=$_CFG['sitename']?>');">加入收藏</a>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <?php $main_menus = $_CFG['main_menuid'] ? $_G['loader']->variable('menu_' . $_CFG['main_menuid']) : ''; ?>    <ul class="tabmenu">
        
<? if(is_array($main_menus)) { foreach($main_menus as $val) { ?>
        <li<? if(SCRIPTNAV==$val['scriptnav']) { ?>
 class="current"<? } ?>
><a href="<?php echo url("{$val['url']}"); ?>"<? if($val['target']) { ?>
 target="<?=$val['target']?>"<? } ?>
 onfocus="this.blur()"><?=$val['title']?></a></li>
        
<? } } ?>
    </ul>
    <div class="search">
        <form method="get" action="<?=URLROOT?>/index.php">
        <input type="hidden" name="act" value="search" />
        <input type="hidden" name="ordersort" value="addtime" />
        <input type="hidden" name="ordertype" value="desc" />
        <input type="hidden" name="searchsubmit" value="yes" />
        <select name="module_flag">
          <option value="item"<? if($MOD['flag']=='item') { ?>
selected="selected"<? } ?>
>身份证号</option>
        </select>
        &nbsp;
        <input type="text" name="keyword" value="" class="input" />&nbsp;
        <input type="image" src="<?=URLROOT?>/<?=$_G['tplurl']?>img/search.png" class="btn" title="搜索" />&nbsp;&nbsp;&nbsp;
        </form>
        <div class="s_right">
            <a href="<?php echo url("member/login"); ?>" id="login_btn_0"><img src="<?=URLROOT?>/<?=$_G['tplurl']?>img/login.png" title="会员登录" alt="login" class="btn" /></a>&nbsp;
            <a href="<?php echo url("item/tag"); ?>"><img src="<?=URLROOT?>/<?=$_G['tplurl']?>img/tag.png" title="TAG标签" alt="tag" class="btn" /></a>&nbsp;
            <a href="<?php echo url("item/rss/catid/{$catid}"); ?>" target="_blank"><img src="<?=URLROOT?>/<?=$_G['tplurl']?>img/rss.png" title="RSS聚合" alt="rss" class="btn" /></a>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?=URLROOT?>/static/javascript/login.js"></script>
<?="\r\n"?>