<?php defined('SYS_PATH') OR die('No direct script access.');

Kohana::module(array(
	'Database',
	'ORM',
	'Captcha',
));

// 用户动作控制器
Route::set('auth-action', 'user/<action>(-<id>).html', array(
		'action' => 'register|login|logout|view|setting|reset|avatar',
		'id' => '\d+',
	))
	->defaults(array(
		'controller' => 'Auth',
	));
