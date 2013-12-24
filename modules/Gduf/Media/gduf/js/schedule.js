$(function(){

	/**
	 * 重新获取课程表
	 */
	$('#gduf-schedule-refresh').click(function(){
		var callback = $(this).data('callback');

		$.ajax({
			url: callback,
			method: 'GET',
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
			success: function(schedule){
				$('#gduf-schedule').html(schedule);
			},
			error: function(){
				alert('系统出现故障，请联系管理员解决问题！');
			}
		});
		
		return false;
	});

});
