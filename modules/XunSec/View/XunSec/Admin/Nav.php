<div id="head" class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="brand" href="<?php echo Route::url('xunsec-admin', array('controller' => 'Page')) ?>">XunSec</a>
			<div class="nav-collapse collapse">
				<ul id="navigation" class="nav">
				<?php if (Kohana::config('Admin.navigation')): ?>
				<?php foreach (($navs = array_reverse(Kohana::config('Admin.navigation'))) AS $title => $nav_data): ?>
					<li class="dropdown<?php if (in_array(Request::current()->controller(), $nav_data['admin_controller'])) { echo ' active'; } ?>">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $title ?></a>
						<ul class="dropdown-menu" data-url="<?php echo Request::current()->url() ?>">
						<?php foreach ($nav_data['links'] AS $title => $link): ?>
							<li<?php if (Request::current()->url() == $link) { echo ' class="active"'; } ?>><?php echo HTML::anchor($link, $title) ?></li>
						<?php endforeach; ?>
						</ul>
					</li>
				<?php endforeach; ?>
				<?php endif; ?>
				</ul>
				<ul id="auth" class="nav pull-right">
					<li><a href="#"><?php echo __('Logged in as :user', array(':user' => $user)); ?></a></li>
					<li><a href="<?php echo URL::base('http') ?>" target="_blank"><?php echo __('Visit Site') ?></a></li>
					<li><?php echo HTML::anchor( Route::url('xunsec-login', array('action'=>'logout')), __('Logout') ) ?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
