var previous_return_type = 1;

/**
 * 建立一個可存取到該file的url
 * PS: 瀏覽器必須支援HTML5 File API
 */
function getObjectURL(file) {
	var url = null ; 
	if (window.createObjectURL!=undefined) { // basic
		url = window.createObjectURL(file) ;
	} else if (window.URL!=undefined) { // mozilla(firefox)
		url = window.URL.createObjectURL(file) ;
	} else if (window.webkitURL!=undefined) { // webkit or chrome
		url = window.webkitURL.createObjectURL(file) ;
	}
	return url ;
}


$(function(){
	$('#information-index-main-nav > li > a').hover( function(){
		$(this).tab('show');
	});
	
	// 下一步
	$('#information-submit-content .go-to-step2').click(function(){
		$('#information-submit-content .step').slideUp().fadeOut();
		$('#information-submit-content #step2').fadeIn();
		$('#information-submit-breadcrumb .step2').addClass('active');
		return false;
	});
	$('#information-submit-content .go-to-step3').click(function(){
		$('#information-submit-content .step').slideUp().fadeOut();
		$('#information-submit-content #step3').fadeIn();
		$('#information-submit-breadcrumb .step3').addClass('active');
		return false;
	});
	$('#information-submit-content .go-to-step4').click(function(){
		$('#information-submit-content .step').slideUp().fadeOut();
		$('#information-submit-content #step4').fadeIn();
		$('#information-submit-breadcrumb .step4').addClass('active');
		return false;
	});
	$('#information-submit-content .go-to-step5').click(function(){
		$('#information-submit-form').submit();
		return false;
	});
	
	// 返回上一步
	$('#information-submit-content .back-to-step3').click(function(){
		$('#information-submit-content .step').slideUp().fadeOut();
		$('#information-submit-content #step3').fadeIn();
		$('#information-submit-breadcrumb .step4').removeClass('active');
		return false;
	});
	$('#information-submit-content .back-to-step2').click(function(){
		$('#information-submit-content .step').slideUp().fadeOut();
		$('#information-submit-content #step2').fadeIn();
		$('#information-submit-breadcrumb .step3').removeClass('active');
		return false;
	});
	$('#information-submit-content .back-to-step1').click(function(){
		$('#information-submit-content .step').slideUp().fadeOut();
		$('#information-submit-content #step1').fadeIn();
		$('#information-submit-breadcrumb .step2').removeClass('active');
		return false;
	});
	
	$('#information-submit-content .wanto-block .block').click(function(){
		if ($(this).hasClass('other')) {
			return false;
		}
		$('#information-submit-content .wanto-block .block').removeClass('active');
		$('#information-submit-content #return_type').val( $(this).data('value') );
		$('#information-submit-content #return_text').val('');
		$(this).addClass('active');
	});
	
	$('#information-submit-content #return_text').keyup(function(){
		if ($(this).val() == '') {
			$('#information-submit-content .wanto-block .block-1').click();
		} else {
			$('#information-submit-content #return_type').val('0');
			$('#information-submit-content .wanto-block .block').removeClass('active');
		}
	});
	
	$("#information-submit-content #image").click(function(){
		$("#information-submit-content #image-upload").click();
		return false;
	});
	/**
	 * 使用HTML5 File API, 來即時預覽image
	 */
	$("#information-submit-content #image-upload").change(function(){
		var objUrl = getObjectURL(this.files[0]) ;
		console.log("objUrl = "+objUrl) ;
		if (objUrl) {
			$("#information-submit-content #image").data('src', objUrl);
			$("#information-submit-content #image").html('<i class="icon-ok"></i> 选择预览图片');
		}
	});
	$('#information-submit-content #image').popover({
		html: true,
		trigger: 'hover',
		content: function () {
			var src = $(this).data('src');
			if (src == '') {
				return '选择你要上传的封面图片';
			}
			return '<img src="'+$(this).data('src') + '" />';
		}
	});
	
	$('#quality-selector .btn').click(function(){
		$('#quality').val( $(this).data('quality') );
	});
});
