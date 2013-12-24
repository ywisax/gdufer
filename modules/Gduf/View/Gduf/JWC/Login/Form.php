<form action="<?php echo Route::url('gduf-jwc-action', array('action' => 'login')) ?>" method="post" class="form-horizontal<?php if (isset($ajax) AND $ajax) { echo ' ajax'; } else {  /*echo ' well';*/ } ?> login-form">
	<fieldset>
		<?php if (isset($ajax) AND $ajax): ?>
		<?php else: ?>
		<legend>
			<?php echo __('Gduf JWC Login') ?>
		</legend>
		<?php endif; ?>
		<div class="control-group<?php if (isset($errors['username'])): ?> error<?php endif; ?>">
			<div class="control-label">
				<label for="username"><?php echo __('Username:') ?></label>
			</div>
			<div class="controls">
				<input id="username" type="text" name="username" placeholder="<?php echo __('Input your username') ?>" class="input-large" value="<?php if (isset($post['username'])) { echo $post['username']; } ?>" />
				<?php if (isset($errors['username'])): ?><span class="help-inline"><?php echo $errors['username'] ?></span><?php endif; ?>
			</div>
		</div>
		<div class="control-group<?php if (isset($errors['password'])): ?> error<?php endif; ?>">
			<div class="control-label">
				<label for="password"><?php echo __('Password:') ?></label>
			</div>
			<div class="controls">
				<input id="password" type="password" name="password" placeholder="<?php echo __('Input your password') ?>" class="input-large" />
				<?php if (isset($errors['password'])): ?><span class="help-inline"><?php echo $errors['password'] ?></span><?php endif; ?>
			</div>
		</div>
		<div class="control-group<?php if (isset($errors['captcha'])): ?> error<?php endif; ?>">
			<div class="control-label">
				<label id="captcha-label" for="captcha-input"><?php echo __('Captcha Code:') ?></label>
			</div>
			<div class="controls">
				<input id="captcha-input" type="text" maxlength="4" name="captcha" class="captcha-input"  /><?php echo Captcha::instance() ?>
				<?php if (isset($errors['captcha'])): ?><br /><span class="help-inline"><?php echo $errors['captcha'] ?></span><?php endif; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<label>便捷：</label>
			</div>
			<div class="controls">
				<label class="checkbox">
					<input type="checkbox" name="remember" value="">
					<?php echo __('Remember me') ?>
				</label>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<input type="hidden" name="redir" value="<?php if (isset($redir)) { echo $redir; } ?>" />
				<button type="submit" id="submit" class="btn btn-primary"><?php echo __('Login') ?></button>
			</div>
		</div>
	</fieldset>
</form>
