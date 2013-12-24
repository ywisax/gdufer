<?php defined('SYS_PATH') OR die('No direct script access.');
// 最后发现每个附件功能真的不行啊啊啊啊啊

Kohana::module(array(
	'Database',
	'ORM',
));

Route::set('attachment-down', 'down/<id>.raw', array(
		'id' => '\d+',
	))
	->defaults(array(
		'controller' => 'Attachment',
		'action' => 'down',
	));
// 附件控制器
Route::set('attachment-action', 'attachment(/<action>(-<id>)).html', array(
		'action' => 'upload|get',
		'id' => '\d+',
	))
	->defaults(array(
		'controller' => 'Attachment',
		'action' => 'index',
	));
