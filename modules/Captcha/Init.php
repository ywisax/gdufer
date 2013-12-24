<?php defined('SYS_PATH') or die('No direct script access.');

// Captcha自带的路由规则，是否可以修改得更好看呢？
Route::set('captcha', 'captcha(/<group>)')
	->defaults(array(
		'controller' => 'Captcha',
		'action' => 'index',
		'group' => NULL
));

