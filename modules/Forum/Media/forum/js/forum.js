
$(function(){
	$('#reply-form-content').submit(function(){
		// 可以改造成ajax发帖
		$('#reply-form-content .content').val( $('#editor').html() );
		return true;
	});
	$('#topic-form').submit(function(){
		// 可以改造成ajax发帖
		$('#topic-form .content').val( $('#editor').html() );
		//return false;
		return true;
	});
});
