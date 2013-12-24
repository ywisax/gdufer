<hr />
<div class="row-fluid">
	<div class="offset2 span2">
		<img src="<?php echo Media::url('img/cancel-delete.png') ?>" />
	</div>
	<div class="span6">
		<h2>登陆后才能继续</h2>
		<hr />
		<p>
			要使用在线课表功能，需要先登录 <a href="http://www.gdufer.com">广金在线</a>！
		</p>
		<p>
			右上角登陆成后 <a href="<?php echo Route::url('auth-action', array('action' => 'setting')) ?>">重新刷新页面即可继续下一步</a>。
		</p>
		<p><a href="<?php echo isset($redirect) ? $redirect : URL::base() ?>"><?php echo __('Continue to visit the previous page') ?></a></p>
	</div>
</div>
<hr />
<div class="text-center">
	<h3>Share what you like, search what you want.</h3>
</div>


