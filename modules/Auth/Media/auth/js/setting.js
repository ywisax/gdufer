
$(function(){

	$('#password').keyup(function(){
		if ($(this).val().length > 0) {
			$('#repeat-password-group').fadeIn();
		} else {
			$('#repeat-password-group').fadeOut();
		}
	});

});
