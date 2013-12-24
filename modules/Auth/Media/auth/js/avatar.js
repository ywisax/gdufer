$(function(){

	var uploadurl = $('#current-avatar').data('uploadurl');
	var cancelImg = $('#current-avatar').data('cancelImg');
	var swf = $('#current-avatar').data('swf');
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
			if (json.image) {
				$('#current-avatar').attr('src', json.image);
			}
            console.log(json.image);
        }
	});
});

