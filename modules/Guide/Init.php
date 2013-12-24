<?php defined('SYS_PATH') OR die('No direct script access.');

Kohana::module(array(
	'Markdown',
	'Guide',
));

// API浏览器
Route::set('guide-api', 'guide/api(/<class>)', array('class' => '[a-zA-Z0-9_]+'))
	->defaults(array(
		'controller' => 'Guide',
		'action'     => 'api',
		'class'      => NULL,
	));

// 向导页面
Route::set('guide-doc', 'guide(/<module>(/<page>))', array(
		'page' => '.+',
	))
	->defaults(array(
		'controller' => 'Guide',
		'action'     => 'doc',
		'module'     => '',
	));
