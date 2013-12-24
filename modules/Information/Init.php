<?php defined('SYS_PATH') OR die('No direct script access.');

Kohana::module(array(
	'Database',
	'ORM',
	'Attachment',
	'Forum', // 跟论坛息息相关
));

// 分类信息首页
Route::set('information-index', 'info.html')
	->protocol('GET')
	->defaults(array(
		'controller' => 'Information',
		'action' => 'index',
	));
// 分类信息控制器
Route::set('information-action', 'info/(<type>-)<action>(-<id>).html', array(
		'type' => 'book|digital',
		'action' => 'view|submit|search|list|mine|comment|delete',
		'id' => '\d+',
	))
	->defaults(array(
		'controller' => 'Information',
	));
