<hr />
<div class="row-fluid">
	<div class="offset2 span2">
		<img src="<?php echo Media::url('img/ok-apply.png') ?>" />
	</div>
	<div class="span6">
		<h2><?php echo __('Register successful') ?></h2>
		<hr />
		<p>
			欢迎加入 <a href="http://www.gdufer.com">广金在线</a>！
		</p>
		<p>
			建议你成功登陆之后 <a href="<?php echo Route::url('auth-action', array('action' => 'setting')) ?>">马上完善你的个人资料，并上传个性头像！</a>
		</p>
		<p><a href="<?php echo isset($redirect) ? $redirect : URL::base() ?>"><?php echo __('Continue to visit the previous page') ?></a></p>
	</div>
</div>
<hr />
<div class="text-center">
	<h3>Share what you like, search what you want.</h3>
</div>

