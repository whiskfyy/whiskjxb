/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
function article_select_category(select,id,all) {
    var catid = $(select).val();
    var cat = $('#catid').empty();
	if(!all) all = false;
	if(all) {
		cat.append("<option value='0'>==全部==</option>");
	}
	if(!catid) return;
    $.each( article_category_sub[catid], function(i, n){
        if(typeof(n)!='undefined') cat.append("<option value='"+i+"'>"+n+"</option>");
    });
}

function article_digg(id) {
    if (!is_numeric(id)) {
        alert('无效的ID'); 
		return;
    }
	$.post(Url('article/detail/id/'+id), {op:"digg",in_ajax:1}, function(result) {
		if (result.match(/\{\s+caption:".*",message:".*".*\s*\}/)) {
            myAlert(result);
        } else if(result == 'OK') {
			$('#digg_num').text(parseInt($('#digg_num').text())+1);
			$('#digg_click').html('谢谢支持');
		} else {
			$('#digg_click').html('谢谢支持');
		}
	});
}