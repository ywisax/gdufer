<?php
XunSec::script('auth/js/setting.js');
?>
<div class="row-fluid">
	<div class="span9">
		<?php if ($success): ?>
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo __('Update your profile information successfully.') ?>
		</div>
		<?php endif; ?>
		<form method="post" class="form-horizontal">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#setting-account" data-toggle="tab"><?php echo __('Account Info') ?></a></li>
				<li><a href="#setting-profile" data-toggle="tab"><?php echo __('Profile Info') ?></a></li>
				<li><a href="#setting-setting" data-toggle="tab"><?php echo __('Account Setting') ?></a></li>
			</ul>
			<div class="tab-content">
				<?php include Kohana::find_file('View', 'Auth.Setting.Account') ?>
				<?php include Kohana::find_file('View', 'Auth.Setting.Profile') ?>
				<?php include Kohana::find_file('View', 'Auth.Setting.Setting') ?>
			</div>
			<hr />
			<button type="submit" class="btn btn-info btn-large "><?php echo __('Update') ?></button>
		</form>
	</div>
	<div class="span3">
	</div>
</div>
