<?php
/**
* @author moufer<moufer@163.com>
* @copyright (c)2001-2009 Moufersoft
* @website www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');
$op = $_POST['op'] ? $_POST['op'] : $_GET['op'];
$GT =& $_G['loader']->model(MOD_FLAG.':gift');
$EX =& $_G['loader']->model(MOD_FLAG.':exchange');

if(check_submit('dosubmit')) {

    $post = $EX->get_post($_POST);
    $exchangeid = $EX->save($post);
    redirect('exchange_post_return', url('exchange/member/ac/m_gift'));

} else {
    $giftid = (int) $_GET['giftid'];
    $gift = $EX->check_exchange($giftid);
}
?>