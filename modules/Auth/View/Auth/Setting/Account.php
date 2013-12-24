<?php
// uploadify
XunSec::style('uploadify/uploadify.css');
XunSec::script('uploadify/jquery.uploadify.min.js');
XunSec::script('auth/js/avatar.js');
?>
<div class="tab-pane active" id="setting-account">
	<!-- 头像 -->
	<div class="control-group">
		<label class="control-label" for="fileInput" title="上传头像">
			<img id="current-avatar" width="54" height="54" src="<?php echo Auth::instance()->get_user()->avatar_img() ?>"
				data-pic=""
				data-uploadurl="<?php echo Route::url('auth-action', array('action' => 'avatar')) ?>"
				data-swf="<?php echo Media::url('uploadify/uploadify.swf') ?>"
				data-cancelImg="<?php echo Media::url('uploadify/uploadify-cancel.png') ?>"
			/>
		</label>
		<div class="controls">
			<input type="file" name="fileInput" id="fileInput" />
		</div>
	</div>
	<hr />
	<!-- 邮箱 -->
	<div class="control-group">
		<label class="control-label" for="email"><?php echo __('Email') ?></label>
		<div class="controls">
			<input disabled type="text" id="email" value="<?php echo $user->email ?>" />
		</div>
	</div>
					<!-- 用户名 -->
					<div class="control-group">
						<label class="control-label" for="username"><?php echo __('Username') ?></label>
						<div class="controls">
							<input disabled type="text" id="username" placeholder="Username" value="<?php echo $user->username ?>" />
						</div>
					</div>
					<!-- 密码 -->
					<div class="control-group<?php if (isset($errors['password'])) { echo ' error'; } ?>">
						<label class="control-label" for="password"><?php echo __('Password') ?></label>
						<div class="controls">
							<input type="password" id="password" name="password" placeholder="<?php echo __('Password') ?>" value="" />
							<?php if (isset($errors['password'])) { ?><span class="help-block"><?php echo $errors['password'] ?></span><?php } ?>
						</div>
					</div>
					<!-- 重复密码 -->
					<div class="control-group<?php if (isset($errors['repeat_password'])) { echo ' error'; } ?>" id="repeat-password-group"<?php if ( ! isset($errors['repeat_password'])) { ?> style="display:none;"<?php } ?>>
						<label class="control-label" for="repeat_password"><?php echo __('Repeat Password') ?></label>
						<div class="controls">
							<input type="password" id="repeat_password" name="repeat_password" placeholder="<?php echo __('Repeat Password') ?>" value="" />
							<?php if (isset($errors['repeat_password'])): ?>
								<span class="help-block"><?php echo $errors['repeat_password'] ?></span>
							<?php else: ?>
								<span class="help-block">更改密码后需要重新登陆</span>
							<?php endif; ?>
						</div>
					</div>
</div>
