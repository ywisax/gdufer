var attachment_count = 0;
var current_page = 1;
var folder_type = 1;

function dump_obj(myObject) {  
	var s = "";  
	for (var property in myObject) {  
		s = s + "\n "+property +": " + myObject[property] ;  
	}  
	alert(s);  
}

// 添加附件上传按钮等
function gduf_add_attachment() {
	attachment_count++;
	var markup = '<div class="fileupload fileupload-new" data-count='+attachment_count+' data-provides="fileupload"><span class="btn btn-file"><span class="fileupload-new">选择文件</span><span class="fileupload-exists">更换</span><input name="FJMC'+attachment_count+'" type="file" /></span><span class="fileupload-preview"></span><a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none">×</a><button class="btn pull-right delete">删除</button></div>';
	$('#send-modal #gduf-attachments').append(markup);
	return false;
}

/**
 * 载入邮件列表
 */
function gduf_mail_page(href) {
	console.log(href);
	$('#gduf-login-form').data('page', href);
	$.ajax({
		url: href,
		method: 'GET',
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(msg){
			mail_box(msg);
		},
		error: function(){
			alert('系统出现故障，请联系管理员解决问题！');
		}
	});
}

function gduf_mail_read( strType,id,iCurrentPage,personId,reply,transmit,transmit,read)
{
	$.ajax({
		url: $('#gduf-login-form').data('read-callback'),
		method: 'GET',
		data: 'foldertype='+strType+'&id='+id+'&page='+iCurrentPage+'&personId='+personId+'&reply='+reply+'&transmit='+transmit+'&readFlag='+read,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(msg){
			// 成功读取邮件
			$('#mail-'+id).removeClass('unread');
			$('#dump').html(msg);
		},
		error: function(){
			alert('系统出现故障，请联系管理员解决问题！');
		}
	});
	return false;
}


function mail_box(data) {
	$('#gduf-login').hide();
	$('#gduf-mailbox').show();
	$('#mail-content').hide().html(data).fadeIn();
	return true;
}

// 加载收信箱
function gduf_mail_list(foldertype, page, searchFieldName, searchKey, order, sortField) {
	// 隐藏login-form
	$('#gduf-login').hide();
	$('#gduf-mailbox').show();
	var mail_list_callback = $('#gduf-login-form').data('list-callback');
	//$('#dump').append('邮件列表 '+mail_list_callback+'<br/>');
	$.ajax({
		url: mail_list_callback,
		method: 'GET',
		//async: false,
		data: 'foldertype='+foldertype+'&page='+page+'&searchFieldName='+searchFieldName+'&searchKey='+searchKey+'&order='+order+'&sortField='+sortField,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(msg){
			$('#mail-content').hide().html(msg).fadeIn();
		},
		error: function(){
			alert('系统出现故障，请联系管理员解决问题！');
		}
	});
	return true;
}

$(function(){

	var login_callback = $('#gduf-login-form').data('login-callback');
	
	var JSESSIONID = '';
	
	// 登陆到旧版
	$('#gduf-login-old').click(function(){
		$('#gduf-login-form').attr('action', 'http://www.gduf.edu.cn/checkuser.jsp');
		$('#gduf-login-form').submit();
		return false;
	});
	
	// 登陆到新版
	$('#gduf-login-new').click(function(){
		var username = $('#gduf-username').val();
		var password = $('#gduf-password').val();

		if (username == '' || password == '') {
			bootbox.confirm('必要的信息未填写完成');
			return false;
		}
		
		$.ajax({
			url: login_callback,
			type: 'POST',
			data: 'username='+username+'&password='+password,
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
			dataType: 'text',
			success: function(response){
				if (response == '') {
					alert('登陆失败！请检查你的账户密码。');
				} else {
					JSESSIONID = response;
					gduf_mail_list(1, 1, '', '', '', '');
				}
			},
			error: function(){
				alert('登陆失败！');
			}
		});
	});
	
	// 新邮件
	//$('#gduf-menu .write a').click(function(){
	//	return false;
	//});
	
	// 收信箱
	$('#gduf-menu .inbox a').click(function(){
		folder_type = 1;
		gduf_mail_list(1, 1, '', '', '', '');
		$('#gduf-menu li').removeClass('active');
		$(this).parent().addClass('active');
		return false;
	});
	// 发信箱
	$('#gduf-menu .outbox a').click(function(){
		folder_type = 2;
		gduf_mail_list(2, 1, '', '', '', '');
		$('#gduf-menu li').removeClass('active');
		$(this).parent().addClass('active');
		return false;
	});
	// 草稿箱
	$('#gduf-menu .draft a').click(function(){
		folder_type = 3;
		gduf_mail_list(3, 1, '', '', '', '');
		$('#gduf-menu li').removeClass('active');
		$(this).parent().addClass('active');
		return false;
	});
	// 垃圾箱
	$('#gduf-menu .trash a').click(function(){
		folder_type = 4;
		gduf_mail_list(4, 1, '', '', '', '');
		$('#gduf-menu li').removeClass('active');
		$(this).parent().addClass('active');
		return false;
	});
	
	// 退出邮箱
	$('#gduf-menu .logout a').click(function(){
		JSESSIONID = '';
		$.get( $('#gduf-login-form').data('logout-callback') );
		//alert('成功退出登陆！');
		$('#gduf-login').show();
		$('#gduf-mailbox').hide();
		return false;
	});
	
	// 分页的一些操作
	$('#mail-content .pagination a').live('click', function(){
		var href = $(this).attr('href');
		if (href === undefined) {
			return false;
		}
		current_page = $(this).data('page');
		//console.log(href);
		gduf_mail_page(href);
		return false;
	});
	
	$('a.read-mail').live('click', function(){

		// 替换标题
		$('#mail-modal-label span').text($(this).attr('title'));

		// 替换正文内容
		var target = $(this).attr('data-target');
		var url = $(this).attr('href');
		$(target).find('.modal-body').html('<div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>').load(url);

		// 把ID插入到modal中去
		$('#mail-modal-id').val($(this).data('id'));
		$('#mail-modal-personId').val($(this).data('personid'));
		
		// 移除高亮
		$('#mail-'+$(this).data('id')).removeClass('unread');
		// 还要替换图片
		var email_img = $('#mail-'+$(this).data('id')+' .status img').attr('src');
		$('#mail-'+$(this).data('id')+' .status img').attr('src', email_img.replace('email-ok.png', 'email-no.png'));

		return false;
	});
	
	// 好像不怎么完美
	$('#mail-modal').on('show', function () {
		//$("body").css('overflow', 'hidden');
	}).on("hidden", function () {
		//$("body").css('overflow', 'auto');
	});

	// 移动到收信箱
	$('#mail-modal .delete').live('click', function(){
		var delete_callback = $('#gduf-login-form').data('rubbish-callback');
		//alert($('#mail-modal-id').val());
		$.post(delete_callback, { C_id:$('#mail-modal-personId').val() }, function(){
			$('#mail-modal .back').click();
			//alert($('#gduf-login-form').data('page'));
			//gduf_mail_page($('#gduf-login-form').data('page'));
			gduf_mail_list(folder_type, current_page, '', '', '', ''); // 这里还要优化一下。
		})
		return false;
	});
	
	// 永久删除邮件，不可恢复
	$('#mail-modal .delete-forever').live('click', function(){
		bootbox.confirm("确定要永久删除这个邮件吗？（不可恢复）", function(result) {
			if (result == true) {
				var delete_callback = $('#gduf-login-form').data('delete-callback');
				//alert($('#mail-modal-id').val());
				$.post(delete_callback, { C_id:$('#mail-modal-personId').val() }, function(){
					$('#mail-modal .back').click();
					//alert($('#gduf-login-form').data('page'));
					//gduf_mail_page($('#gduf-login-form').data('page'));
					gduf_mail_list(folder_type, current_page, '', '', '', ''); // 这里还要优化一下。
				})
			}
		}); 
		return false;
	});
	
	// 点击tr直接打开邮件啦
	$('.mail-tr .sender, .mail-tr .title, .mail-tr .dateline').live('click', function(){
		$(this).parent().find('.read-mail').trigger('click');
		return false;
	});

	// 如果session为空，那就是要登陆咯
	if (CURRENT_JSESSIONID == '') {
		$('#gduf-mailbox').hide();
	} else {
		$('#gduf-login').hide();
		$('#gduf-menu .inbox a').trigger('click');
	}
	
	// 默认给附加上一个上传按钮
	gduf_add_attachment();
	
	// 添加附件
	$('button.add-attachment').live('click', function(){
		gduf_add_attachment();
	});
	// 添加附件
	$('.fileupload .delete').live('click', function(){
		$(this).parent().remove();
		return false;
	});
	$('#search-user').live('click', function(){
		alert('不支持群发喔。保持邮箱清洁，人人有责。');
		return false;
	});
	
	// 发送邮件
	$('#send-modal #gduf-mail-send-submit').click(function(){
		var form = $('#send-modal form');
		var callback = form.data('callback');
		alert('因部分同学滥用群发功能，暂停使用发件功能。');
		console.log(callback);
		return false;
	});
	
});
