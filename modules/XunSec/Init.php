<?php defined('SYS_PATH') OR die('No direct access allowed.');

Kohana::module(array(
	'Auth',
	'ORM',
	'Media',
	'Database',
	'Markdown',
	'Twig',
	'Pagination', // 用到分页
));

// 后台登陆路由
Route::set('xunsec-login', 'admin/<action>.html', array('action'=>'login|logout|lang'))
	->defaults(array(
		'controller' => 'Admin',
		'directory'  => 'XunSec',
	));

// 后台路由
Route::set('xunsec-admin', 'admin(/<controller>(/<action>(/<params>))).html', array('params'=>'.*'))
	->defaults(array(
		'controller' => 'Page',
		'action'     => 'index',
		'directory'  => 'XunSec'
	));

// 默认路由
Route::set('default', '(<path>)', array(
		'path' => '.*',
	))
	->defaults(array(
		'controller' => 'XunSec',
		'action'     => 'view',
	));
