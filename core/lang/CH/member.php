<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
return array(

    'member_operation_title' => '我的助手',
    'member_login_title' => '会员登录',
    'member_reg_title' => '用户注册',
    'member_changepw_title' => '修改密码',

    'member_not_login' => '对不起，本次操作需要登录后才能执行，请先登录网站。',
    'member_login_logined' => '您已经登录，无需再次登录。',
    'member_login_lost' => '对不起，您填写的帐号有误，请返回重写。',
    'member_op_not_login' => '对不起，请先登录网站后，再进行操作。',
    'member_forget_invalid' => '您的账户或者Email地址有误，不能使用取回密码功能，如有疑问请与管理员联系。',
    'member_login_succeed' => '登录成功，欢迎您回来！',
    'member_empty' => '用户不存在。',

    'member_reg_ajax_name_empty' => '<font color="red">您未填写用户名</font>',
    'member_reg_ajax_name_exists' => '<font color="red">您填写的用户名已存在</font>',
    'member_reg_ajax_name_normal' => '<font color="green">您可以使用这个用户名</font>',
    'member_reg_ajax_email_empty' => '<font color="red">您未填写的E-mail地址</font>',
    'member_reg_ajax_email_invalid' => '<font color="red">您填写的E-mail地址格式不正确</font>',
    'member_reg_ajax_email_exists' => '<font color="red">您填写的E-mail地址已存在</font>',
    'member_reg_ajax_email_normal' => '<font color="green">您可以使用这个E-Mail地址</font>',

    'member_post_empty_name' => '对不起，您未填写用户名，请返回填写。',
    'member_post_exists_name' => '对不起，您填写的用户名已存在，请返回重写。',
    'member_post_empty_pw' => '对不起，您未填写密码，请返回填写。',
    'member_post_empty_pw_len' => '对不起，你设置的密码长度过短，不能少于 %d 个字符，请返回修改。',
    'member_post_empty_pw_error' => '对不起，您输入的密码不正确。',
    'member_post_empty_eq_pw' => '对不起，您2次输入的密码不一致，请返回填写。',
    'member_post_empty_email' => '对不起，您未填写email或者格式错误，请返回重写。',
    'member_post_exists_email' => '对不起，您填写的e-mail已存在，请返回重写。',
    'member_post_empty_group' => '对不起，您未选择一个有效的会员组。',

    'member_reg_logined' => '您目前处于登录状态，无法注册新帐户，请退出后再进行注册。',
    'member_reg_closed' => '对不起，网站关闭了会员注册功能。',
    'member_reg_name_len_max' => '<font color="red">用户名不能大于15个字符</font>',
    'member_reg_name_sensitive_char' => '用户名不得包含(,)、(*)、(")、([TAB])、([SPACE])、([\r])、([\n])、(&lt;)、(&gt;)、(&amp;)其中之一',
    'member_reg_name_limit' => '<font color="red">管理员限制了您输入的用户名进行注册</font>',
    'member_reg_pw_limit' => '<font color="red">密码中只能包含数字，字母以及"_ ~ ! @ #"!</font>',
    'member_reg_succeed' => '注册成功，欢迎您来到我们的大家庭！',
    'member_reg_msg_subject' => '欢迎注册成为我们的会员！',
    'member_reg_msg_content' => "敬爱的会员：\$username\r\n欢迎您注册成为 [\$sitename] 会员。\r\n\r\n\$sitename\r\n\$time",

    'member_pm_inbox' => '收件箱',
    'member_pm_outbox' => '发件箱',
    'member_pm_not_exists' => '对不起，短信息不存在。',
    'member_pm_not_exists_2' => '对不起，短信息不存在或已删除。',
    'member_pm_dnot_send_self' => '对不起，您无法给自己发送短消息。',
    'member_pm_empty_recv' => '对不起，您未填写发送对象。',
    'member_pm_empty_subject' => '对不起，您未填写短信主题。',
    'member_pm_empty_content' => '对不起，您未填写短信内容。',
    'member_pm_strlen_subject' => '对不起，您填写的短信息主题不能超过 %d 个字符，请返回修改。',
    'member_pm_strlen_content' => '对不起，您填写的短信息内容不能超过 %d 个字符。',
    'member_pm_send_total' => '对不起，单次发送短信息不能超过 %d 个。',
    'member_pm_empty_member' => '对不起，您的发送对象不存在。',

	'member_point_exchange_des' => '积分兑换',
	'member_point_pay_des' => '积分充值',
	'member_point_rmb' => '现金',
    'member_point_less_point_self' => '很遗憾，您的积分不足 %d 个，无法完成本次操作。',
    'member_point_less_coin_self' => '很遗憾，您的金币不足 %d 个，无法完成本次操作。',
    'member_point_less_point' => '很遗憾，%s 的积分不足 %d 个，无法完成本次操作。',
    'member_point_less_coin' => '很遗憾，%s 的金币不足 %d 个，无法完成本次操作。',

	'member_assistant_menuid' => '对不起，系统未设置我的助手菜单组，请到后台设置。',

    'member_effect_empty_id' => '对不起，你未设置id号。',
    'member_effect_unkown_idtype' => '未知的会员参与种类。',
    'member_effect_unkown_effect' => '不存在的会员参与类型。',
    'member_effect_submitted' => '您已经做出过选择。',

    'member_friend_exists' => '您添加的好友已经存在了。',
    'member_friend_uid_invalid' => '您添加的好友ID无效。',
    'member_friend_not_found' => '您添加的好友不存在。',
    'member_friend_add_self' => '不能添加自己为好友。',

    'member_friend_msg_subject' => '你是我的好友啦!',
    'member_friend_msg_message' => '我已经把你加入我的好友了，很高兴认识你，请多多指教!',

    'member_forget_mail_subject' => '{sitename}会员，找回密码',
    'member_forget_mail_message' => "敬爱的{username}：\r\n您于 {nowtime} 使用了找回密码功能，请及时通过下面的链接来更新你的密码，本次链接有效期为{endday}天，将于 {endtime} 后过期。\r\n密码更新地址：{forgeturl}\r\n请将密码更新地址复制到浏览器地址栏中打开。\r\n\r\n{sitename}({siteurl})\r\n{nowtime}",
    'member_forget_mail_succeed' => '密码更新操作已发送到您的邮箱中，请登录您的邮箱查收邮件，及时更改密码。',

    'member_getpassword_param_empty' => '对不起，找回密码的参数无效。',
    'member_getPassword_empty' => '找回密码的信息不存在。',
    'member_getpassword_timeout' => '对不起，本次密码找回已超时，请重新操作一次找回密码。',
    'member_getpassword_invalid' => '对不起，找回密码信息验证失败。',
    'member_getpassword_username_empty' => '对不起，用户信息不存在。',
    'member_getpassword_succeed' => '恭喜您，密码更新成功！',

    'member_usergroup_empty'  => '对不起，用户组信息不存在。',

    'member_access_forbidden' => '对不起，您的帐号已被管理员设置禁止访问本站。<a href="'.url('member/login/op/logout').'">点击这里退出</a>',
);
?>