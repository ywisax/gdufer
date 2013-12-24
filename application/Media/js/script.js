$(function(){

    $(window).scroll(function(){
        // 添加一个类表明当前正在滚动
        if ($(this).scrollTop() > 100) {
            $(".navbar.navbar-fixed-top").addClass("scroll");
        } else {
            $(".navbar.navbar-fixed-top").removeClass("scroll");
        }

        // 额
        if ($(this).scrollTop() > 300) {
            $('.scrolltop').fadeIn();
        } else {
            $('.scrolltop').fadeOut();
        }        
    });
    // scroll back to top btn
    $('.scrolltop').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 700);
        return false;
    });

	// 加载完毕后在底部加载一个空的div
	var body_loading_init = function() {
		$('body').append('<div id="loading"></div>');
	}
	body_loading_init();
	
	$("#loading").ajaxStart(function(){
		$(this).fadeIn();
	});

	$("#loading").ajaxStop(function(){
		$(this).fadeOut();
	});
	
	// 注册按钮，ajax啊
	$('#nav-register a').click(function(){
		var callback = $(this).data('callback');
		console.log(callback);
		$.get(callback, function(data) {
			$(data).modal();
		});
		return false;
	});
	
	// 登陆按钮，ajax啊
	$('#nav-login a').click(function(){
		var callback = $(this).data('callback');
		console.log(callback);
		$.get(callback, function(data) {
			$(data).modal();
		});
		return false;
	});
	
	/**
	 * 用户信息
	 */
	$('a.popuser').popover({
		html: true,
		trigger: 'hover',
		delay: { show: 700, hide: 100 },
		content: function () {
			var uid = $(this).data('uid');
			//return '<strong>学号：</strong>121685218<br /><strong>姓名：</strong>虎劲淘<br /><strong>格言：</strong>How do you think about me ?';
			return '<strong>UID：</strong>'+uid+'<br /><strong>格言：</strong>广金在线，分享你所爱';
		}
	});
});
