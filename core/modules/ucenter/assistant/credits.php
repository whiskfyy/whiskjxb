<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

if(!$MOD['uc_exange']||!defined('IN_UC')) {
    redirect('ucenter_exchange_disable');
}

if(check_submit('dosubmit')) {

    $outextcredits = unserialize($MOD['outextcredits']);

    $tocredits = trim($_POST['tocredits']);
    if($outextcredits[$tocredits]['creditsrc'] != 0) {
        redirect('ucenter_exchange_intvalid');
    }

    $amount = intval($_POST['amount']);
    !$tocredits && redirect('ucenter_exchange_dest_empty');
    $amount <= 0 && redirect('ucenter_exchange_coin_less0');

    list($tmp_uid) = uc_user_login($user->username, $_POST['password_credits']);
    $tmp_uid <= 0 && redirect('ucenter_exchange_password_invalid');
    unset($_POST['password_credits']);

    $user->coin < $amount && redirect("ucenter_exchange_coin_not_enough");
    $netamount = floor($amount / $outextcredits[$tocredits]['ratio']);
    if($amount > 0 && $netamount == 0) redirect("ucenter_exchange_revenue_empty");
    list($toappid, $tocredits) = explode('|', $tocredits);

    if(!$ucresult = uc_credit_exchange_request($user->uid, 0, $tocredits, $toappid, $netamount)) {
        redirect('ucenter_exchange_intvalid');
    }

    $pt =& $_G['loader']->model('member:point');
    $pt->update_point2($user->uid,'coin', -$amount, 'ucenter_exchange_des');
    unset($pt, $amount, $tocredits);

    redirect('global_op_succeed', url('ucenter/member/ac/credits'));

} else {

    $extcredits_exchange = array();
    $outextcredits = unserialize($MOD['outextcredits']);

}
?>