<?php
if ( ! isset($errors))
{
	$errors = array();
}
?>
<form <?php if (isset($ajax) AND $ajax) { ?>action="<?php echo Route::url('auth-action', array('action' => 'register')) ?>"<?php } ?> method="post" class="form-horizontal<?php if (isset($ajax) AND $ajax) { echo ' ajax'; } else {  /*echo ' well';*/ } ?> login-form">
	<fieldset>
	<?php if (isset($ajax) AND $ajax): ?>
	<?php else: ?>
		<legend>
			<?php echo __('Register') ?>
			<small class="pull-right"><?php echo HTML::anchor(Route::url('auth-action', array('action' => 'login')), __('Log in')) ?></small>
		</legend>
	<?php endif; ?>
		<div class="control-group<?php if (isset($errors['email'])): ?> error<?php endif; ?>">
			<div class="control-label">
				<label for="email"><?php echo __('E-Mail:') ?></label>
			</div>
			<div class="controls">
				<input id="email" type="text" name="email" placeholder="<?php echo __('Your email address') ?>" class="input-large" value="<?php if (isset($post['email'])) { echo $post['email']; } ?>" />
				<?php if (isset($errors['email'])): ?><br /><span class="help-inline"><?php echo $errors['email'] ?></span><?php endif; ?>
			</div>
		</div>
		<div class="control-group<?php if (isset($errors['username'])): ?> error<?php endif; ?>">
			<div class="control-label">
				<label for="username"><?php echo __('Username:') ?></label>
			</div>
			<div class="controls">
				<input id="username" type="text" name="username" placeholder="<?php echo __('Your login username') ?>" class="input-large" value="<?php if (isset($post['username'])) { echo $post['username']; } ?>" />
				<?php if (isset($errors['username'])): ?><br /><span class="help-inline"><?php echo $errors['username'] ?></span><?php endif; ?>
			</div>
		</div>
		<div class="control-group<?php if (isset($errors['password'])): ?> error<?php endif; ?>">
			<div class="control-label">
				<label for="password"><?php echo __('Password:') ?></label>
			</div>
			<div class="controls">
				<input id="password" type="password" name="password" placeholder="<?php echo __('Your login password') ?>" class="input-large" />
				<?php if (isset($errors['password'])): ?><br /><span class="help-inline"><?php echo $errors['password'] ?></span><?php endif; ?>
			</div>
		</div>
		<div class="control-group<?php if (isset($errors['password_confirm'])): ?> error<?php endif; ?>">
			<div class="control-label">
				<label for="password_confirm"><?php echo __('Password Confirm:') ?></label>
			</div>
			<div class="controls">
				<input id="password_confirm" type="password" name="password_confirm" placeholder="<?php echo __('Re-type your password') ?>" class="input-large" />
				<?php if (isset($errors['password_confirm'])): ?><br /><span class="help-inline"><?php echo $errors['password_confirm'] ?></span><?php endif; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<label for="stuno"><?php echo __('Student NO:') ?></label>
			</div>
			<div class="controls">
				<input id="stuno" type="text" name="stuno" placeholder="<?php echo __('Your real student number') ?>" class="input-large" value="<?php if (isset($post['stuno'])) { echo $post['stuno']; } ?>" />
				<?php if (isset($errors['stuno'])): ?><br /><span class="help-inline"><?php echo $errors['stuno'] ?></span><?php endif; ?>
			</div>
		</div>
		<div class="control-group<?php if (isset($errors['captcha'])): ?> error<?php endif; ?>">
			<div class="control-label">
				<label for="captcha" id="captcha-label"><?php echo __('Captcha Code:') ?></label>
			</div>
			<div class="controls">
				<input id="captcha-input" type="text" maxlength="4" name="captcha" class="captcha-input"  /><?php echo Captcha::instance() ?>
				<?php if (isset($errors['captcha'])): ?><span class="help-inline"><?php echo $errors['captcha'] ?></span><?php endif; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<input type="hidden" name="redir" value="<?php if (isset($redir)) { echo $redir; } ?>" />
				<button type="submit" class="btn btn-primary"><?php echo __('Quick Register') ?></button>
			</div>
		</div>
	</fieldset>
</form>

