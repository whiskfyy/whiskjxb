<?php
/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
!defined('IN_MUDDER') && exit('Access Denied');

$P =& $_G['loader']->model(MOD_FLAG.':picture');

if(!$_POST['dosubmit']) {

    //权限验证
    $user->check_access('item_pictures', $P);

	$_G['loader']->helper('form');
    if($_GET['op'] == 'multi') {
        if(!$MOD['multi_upload_pic']) redirect('item_upload_multi_off');
        $multi_num = $MOD['multi_upload_pic_num'] > 1 && $MOD['multi_upload_pic_num'] < 20 ? $MOD['multi_upload_pic_num'] : 5;
    }

	if($sid = (int)$_GET['sid']) {
        //redirect(lang('global_sql_keyid_invalid', 'sid'));
        $subject = $P->check_post_before($sid);
        $pid = $subject['pid'];

        $category = $P->variable('category');
        $modelid = $category[$pid]['modelid'];
        $catcfg = $category[$pid]['config'];
        $model = $P->variable('model_' . $modelid);
    }
    $tplname = 'pic_upload';

} else {

    $post = $P->get_post($_POST, FALSE);
    $result = $P->save($post, $_POST['multi'] == 'yes');
    if(_post('do') == 'review_upload') {
        echo $result;
        output();
    }

    redirect(RETURN_EVENT_ID, url('item/member/ac/m_picture'));
}
?>