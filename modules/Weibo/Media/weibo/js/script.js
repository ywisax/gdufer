var word_limit = 140;

var pic_link_hover_html = '点击上传图片';

$(function(){

	// 微博发布页面字数限制和提醒
	$('#weibo-post-area').keyup(function(){
		// 0的时候，就是不用检验的时候
		if (word_limit == 0) {
			return false;
		}
		var cur_length = $(this).val().length;
		if (cur_length > word_limit) {
			$(this).val( $(this).val().substr(0, word_limit) );
			return false;
		} else {
			$('#weibo-post-words small').text( $(this).val().length );
			return true;
		}
	});
	
	// 长微博什么的
	$('#weibo-functions a.long').toggle(function(){
		word_limit = 0;
		$('#weibo-post-area').animate({height:'500px'});
		$('#weibo-functions a.long img').attr('src', $('#weibo-functions a.long img').data('img2'));
		$('#weibo-post-words small').text('长文本');
		$('#weibo-is-long').val('1');
		return false;
	}, function(){
		word_limit = 140;
		$('#weibo-post-area').animate({height:'80px'});
		$('#weibo-functions a.long img').attr('src', $('#weibo-functions a.long img').data('img1'));
		$('#weibo-post-words small').text('0');
		$('#weibo-is-long').val('0')
		$('#weibo-post-area').keyup();
		return false;
	});
	
	$('#weibo-send-button').click(function(){
		var content = $('#weibo-post-area').val();
		var sendurl = $(this).data('send');
		var is_long_input = $('#weibo-is-long').val();
		var img_id_input = $('#weibo-image-id').val();
		
		// 长度小于1？？
		if (content.length < 1) {
			bootbox.alert("内容不能为空！");
		} else {
			$.post(sendurl, { feed:content, is_long:is_long_input, img_id:img_id_input }, function(response){
				if (response == '') {
					response = '微博发布成功！你可以访问<a target="_blank" href="http://weibo.com/ibnuzer">http://weibo.com/ibnuzer</a>进入查看';
				}
				bootbox.alert(response);
			});
		}
		return false;
	});

	// 引用
	$('#weibo-functions a.tag').click(function(){
	});
	
	var emotions = null;

	// 表情
	$('#weibo-functions a.smile').click(function(){
		return false;
	});
	$('#weibo-functions a.smile').popover({
		html: true,
		trigger: 'click',
		placement: 'bottom',
		title: '常用表情',
		content: function () {
			if (emotions === null) {
				$.ajax({
					type: 'GET',
					url: $(this).data('url'),
					async: false,
					success: function(res){
						emotions = res;
					}
				});
				return emotions;
			} else {
				//console.log(res);
				//return '表情列表';
				return emotions;
			}
		}
	});
	$('#weibo-smile-list li img').live('click', function(){
		var emotion = $(this).data('emotion');
		var content = $('#weibo-post-area').val();
		$('#weibo-post-area').val(content+emotion).keyup();
		return false;
	});
	
	// 标签
	$('#modal-tag-submit').click(function(){
		var tag = $('#modal-tag-input').val();
		tag = $.trim(tag);
		if (tag) {
			var content = $('#weibo-post-area').val();
			$('#weibo-post-area').val(content+'#'+tag+'#').keyup();
		}
		$(this).next().click();
		return false;
	});
	$('#modal-tag .badge').click(function(){
		$('#modal-tag-input').val( $(this).data('topic') );
	});

	var uploadurl = $('#weibo-functions a.pic').data('uploadurl');
	var cancelImg = $('#weibo-functions a.pic').data('cancelImg');
	var swf = $('#weibo-functions a.pic').data('swf');
	$('#fileInput').uploadify({
		//以下参数均是可选
		'swf'  : swf,   //指定上传控件的主体文件，默认‘uploader.swf’
		'uploader'    : uploadurl,       //指定服务器端上传处理文件，默认‘upload.php’
		'cancelImg' : cancelImg,   //指定取消上传的图片，默认‘cancel.png’
		'auto'      : true,               //选定文件后是否自动上传，默认false
		'folder'    : '/uploads',         //要上传到的服务器路径，默认‘/’
		'multi'     : false,               //是否允许同时上传多文件，默认false
		'fileTypeDesc' : '图片文件',  //出现在上传对话框中的文件类型描述
		'fileTypeExts'   : '*.gif; *.jpg; *.png',      //控制可上传文件的扩展名，启用本项时需同时声明fileDesc
		'sizeLimit': 86400,           //控制上传文件的大小，单位byte
		'simUploadLimit':1,         //多文件上传时，同时上传文件数目限制
		'buttonText': '上传图片',
		'removeCompleted': true,
		
		'onSelect' : function(file) {
			//$('#fileInput').uploadify('disable', true);
		},
		'onCancel' : function(file) {
			//$('#fileInput').uploadify('disable', false);
		},
		'onUploadSuccess' : function(file, data, response) {
			var json = $.parseJSON(data);
            console.log('文件 ' + file.name + ' 上传成功。图片ID：' + json.id + ' ，图片地址：' + json.image);
			// 获取得到地址后
			pic_link_hover_html = '<img width="200" src="'+json.image+'" />';
			// 获取到ID后
			$('#weibo-image-id').val(json.id);
        }
	});
	
	// 图片那里
	$('#weibo-functions a.pic').popover({
		html: true,
		trigger: 'hover',
		content: function () {
			var img = $(this).data('pic');
			if (img == '' || img == null) {
				return pic_link_hover_html;
			}
			return img;
			//return '<img src="'+$(this).data('img') + '" />';
		}
	});
	$('#weibo-functions a.pic').toggle(function(){
		$('#upload-block').show('slow');
		return false;
	}, function(){
		$('#upload-block').hide('slow');
		return false;
	});
	
});
