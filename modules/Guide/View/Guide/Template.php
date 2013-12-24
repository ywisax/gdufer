<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="zh">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title><?php echo $title ?> | <?php echo __('Kohana User Guide'); ?></title>
<?php foreach ($styles AS $style => $media) echo HTML::style($style, array('media' => $media), NULL, TRUE), "\n" ?>
<!--[if lt IE 7]>
<?php echo HTML::script(Media::url('ie7/IE7.js'))."\r\n" ?>
<![endif]-->
<!--[if lt IE 8]>
<?php echo HTML::script(Media::url('ie7/IE8.js'))."\r\n" ?>
<![endif]-->
<!--[if lt IE 9]>
<?php echo HTML::script(Media::url('ie7/IE9.js'))."\r\n" ?>
<![endif]-->
</head>
<body>
	<div id="guide-header">
		<div class="container">
			<a href="<?php echo URL::base() ?>" id="guide-logo">
				<img src="<?php echo Media::url('guide/img/kohana.png') ?>" alt="Kohana <?php echo __('User Guide') ?>" />
			</a>
			<div id="guide-menu">
				<ul>
					<li class="index first">
						<?php echo HTML::anchor(URL::base(), __('Website Index')) ?>
					</li>
					<li class="guide">
						<?php echo HTML::anchor(Route::url('guide-doc'), __('User Guide')) ?>
					</li>
					<?php if (Kohana::config('Guide.api_packages')): ?>
					<li class="api">
						<?php echo HTML::anchor(Route::url('guide-api'), __('API Browser')) ?>
					</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>

	<div id="guide-content">
		<div class="wrapper">
			<div class="container">
				<div class="span-22 prefix-1 suffix-1">
					<ul id="guide-breadcrumb">
						<?php foreach ($breadcrumb AS $link => $title): ?>
							<?php if (is_string($link)): ?>
							<li><?php echo HTML::anchor($link, $title, NULL, NULL, TRUE) ?></li>
							<?php else: ?>
							<li class="last"><?php echo $title ?></li>
							<?php endif ?>
						<?php endforeach ?>
					</ul>
				</div>
				<div class="span-6 prefix-1">
					<div id="guide-topics">
						<?php echo $menu ?>
					</div>
				</div>
				<div id="guide-body" class="span-16 suffix-1 last">
					<?php echo $content ?>
					<?php include Kohana::find_file('View', 'Guide.SocialComment') ?>
				</div>
			</div>
		</div>
	</div>

	<div id="guide-footer">
		<div class="container">
			<div class="span-12">
			<?php if (isset($copyright)): ?>
				<p><?php echo $copyright ?></p>
			<?php else: ?>
				&nbsp;
			<?php endif ?>
			</div>
			<div class="span-12 last right">
			<p><a href="http://kohanaframework.org/" rel="nofollow"><?php echo __('Powered by :provider', array(
				':provider' => 'Kohana v3.3'
			)) ?></a></p>
			</div>
		</div>
	</div>

<?php foreach ($scripts AS $script) echo HTML::script($script, NULL, NULL, TRUE), "\n" ?>
</body>
</html>
