<?php defined('SYS_PATH') OR die('No direct script access.');

Kohana::module(array(
	'ORM',
	'Database', // ORM肯定用到数据库啦
	'Media',
	'Pagination', // 用到分页
));

// 联系我们-表单
Route::set('contact-action', 'contact(-<action>).html', array(
		'action' => 'form|post',
	))
	->defaults(array(
		'controller' => 'Contact',
		'action' => 'form',
	));
