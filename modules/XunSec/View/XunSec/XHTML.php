<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">
<head>
	<title><?php echo XunSec::page('title') ?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="description" content="<?php echo XunSec::page('metadesc') ?>" />
	<meta name="keywords" content="<?php echo XunSec::page('metakw') ?>" />
	<!--[if lt IE 9]><script src="<?php echo Media::url('html5shiv.js') ?>"></script><![endif]-->
	<!-- HEAD文件包含-begin -->
	<?php echo XunSec::style_render(); ?>
	<?php echo XunSec::meta_render(); ?>
	<!-- HEAD文件包含-end -->
</head>
<body<?php if (XunSec::$adminmode) { echo ' id="xunsec-admin"'; } ?>>
	<?php if (XunSec::$adminmode): ?>
	<!-- Admin mode header -->
	<div id="xunsec-header">
		<p>
			<?php echo HTML::anchor(Route::url('xunsec-admin', array('controller' => 'Page')), '&laquo; '.__('Back')) ?> | 
			<?php echo __('You are editing :page', array(':page' => '<strong>'.XunSec::page('name').'</strong>')) ?> |
			<?php echo HTML::anchor(Route::url('xunsec-admin', array(
				'controller' => 'Page',
				'action' => 'meta',
				'params' => XunSec::page('id'),
			)),__('Edit meta data')) ?>
		</p>
	</div>
	<!-- End Admin mode header -->
	<?php endif; ?>
	<!-- Begin Page Layout Code -->
	<?php //echo $layoutcode ?>
	<?php echo trim($layoutcode) ?>
	<!-- End Page Layout Code -->
	<?php
	if (Kohana::$profiling)
	{
		Profiler::render('Console');
	}
	?>
	<?php echo XunSec::script_render(); ?>
</body>
</html>
