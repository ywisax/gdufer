<?php defined('SYS_PATH') or die('No direct script access.');

Kohana::module(array(
	'Image',
	'Attachment',
));

Route::set('media', 'media/<filepath>', array(
		'filepath' => '.*', // Pattern to match the file path
	))
	->defaults(array(
		'controller' => 'Media',
		'action'     => 'render',
	));
