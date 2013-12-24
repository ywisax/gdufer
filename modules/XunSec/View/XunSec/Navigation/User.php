<ul class="nav pull-right">
<?php if (Auth::instance()->logged_in()): ?>
	<li id="nav-setting">
		<a class="popuser" href="<?php echo Route::url('auth-action', array('action' => 'setting')) ?>" data-placement="bottom" data-username="<?php echo Auth::instance()->get_user()->username ?>" data-uid="<?php echo Auth::instance()->get_user()->id ?>">
			<img class="avatar" src="<?php echo Auth::instance()->get_user()->avatar_img() ?>" />
			<?php echo Auth::instance()->get_user()->username ?>
		</a>
	</li>
	<li id="nav-logout">
		<a href="<?php echo Route::url('auth-action', array('action' => 'logout')) ?>"><?php echo __('Logout') ?></a>
	</li>
<?php else: ?>
	<li id="nav-register">
		<a href="#" data-callback="<?php echo Route::url('auth-action', array('action' => 'register')) ?>" data-toggle="modal"><?php echo __('Sign up') ?></a>
	</li>
	<li id="nav-login">
		<a href="#" data-callback="<?php echo Route::url('auth-action', array('action' => 'login')) ?>" data-toggle="modal"><?php echo __('Log in') ?></a>
	</li>
<?php endif; ?>
</ul>
