<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title><?php echo (isset($title) ? __("Admin") . " - " . $title : __("Admin")); ?></title>
	<?php echo HTML::style( Media::url('css/admin.css'), array('media' => 'screen', 'charset' => 'utf-8'))."\n"; ?>
</head>
<body>
	<div id="admin_login_form">
  
			<h1><?php echo __('Login') ?></h1>
			
			<?php include Kohana::find_file('View', 'XunSec.Error') ?>
			<?php echo Helper_Form::open(NULL, array('id' => 'login')) ?>
			
			 <p><label><?php echo __('Username:') ?></label> <?php echo Helper_Form::input('username', isset($post['username']) ? $post['username'] : '') ?></p>
			 <p><label><?php echo __('Password:') ?></label> <?php echo Helper_Form::password('password') ?></p>
			
			<p>
				<button type="submit"><?php echo __('Login') ?></button>
			</p>
			
			<?php echo Helper_Form::close() ?>
	</div>
</body>
</html>
