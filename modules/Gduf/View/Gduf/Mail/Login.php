<?php
if ( ! isset($gduf_login_form_in_welcom_page))
{
	$gduf_login_form_in_welcom_page = FALSE;
}
?>
<div id="gduf-login" class="row-fluid">
	<div class="<?php if ($gduf_login_form_in_welcom_page) { ?>welcome-mail<?php } else { ?>offset3 span6<?php } ?>" id="gduf-login-block">
		<form method="post" id="gduf-login-form" class="form-horizontal well login-form"
			data-page=""
			data-login-callback="<?php echo Route::url('gduf-mail-action', array('action' => 'login')) ?>"
			data-list-callback="<?php echo Route::url('gduf-mail-action', array('action' => 'list')) ?>"
			data-logout-callback="<?php echo Route::url('gduf-mail-action', array('action' => 'logout')) ?>"
			data-mail-read-callback="<?php echo Route::url('gduf-mail-action', array('action' => 'read')) ?>"
			data-delete-callback="<?php echo Route::url('gduf-mail-action', array('action' => 'delete')) ?>"
			data-rubbish-callback="<?php echo Route::url('gduf-mail-action', array('action' => 'rubbish')) ?>"
			data-send-callback="<?php echo Route::url('gduf-mail-action', array('action' => 'send')) ?>"
		>
			<fieldset>
				<legend>
					登陆新版校内邮箱
					<small class="pull-right"><a target="_blank" href="http://www.gduf.edu.cn/">使用旧版邮箱</a></small>
				</legend>
				<div class="control-group">
					<div class="control-label">
						<label for="gduf-username">用户名：</label>
					</div>
					<div class="controls">
						<input type="text" id="gduf-username" name="username" placeholder="用户名" class="input-large" required />
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label for="gduf-password">密 码：</label>
					</div>
					<div class="controls">
						<input type="password" id="gduf-password" name="password" placeholder="密 码" class="input-large" required />
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<a id="gduf-login-new" class="btn btn-primary">登陆到新版</a>
						<a id="gduf-login-old" class="btn">登陆到旧版</a>
					</div>
				</div>
				<div class="control-group notice">
					<p><span class="label label-warning">提示</span> 目前本邮箱系统还只支持读信功能，暂未开放发送和回复功能（防垃圾邮件）。</p>
				</div>
			</fieldset>
		</form>
	<?php if ($gduf_login_form_in_welcom_page): ?>
		<p class="new-notice">目前新版邮箱正在内测中，浏览者可以暂时使用旧版登陆。要体验新版邮箱，请点击此处给本站留言获取资格。</p>
	<?php endif; ?>
	</div>
</div>
