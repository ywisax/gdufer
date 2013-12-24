<?php defined('SYS_PATH') OR die('No direct script access.');

define('GDUF_DOMAIN', 'http://www.gduf.edu.cn/');

Kohana::module(array(
	'ORM',
	'Database', // ORM肯定用到数据库啦
	'Media',
	'Pagination', // 用到分页
));

// 下面添加路由

// 广金邮箱
Route::set('gduf-mail', 'gduf/mail.html')
	->defaults(array(
		'directory' => 'Gduf',
		'controller' => 'Mail',
		'action' => 'index',
	));
Route::set('gduf-mail-action', 'gduf/mail-<action>.html', array(
		'action' => 'login|list|logout|delete|rubbish|send|read|bind',
	))
	->defaults(array(
		'directory' => 'Gduf',
		'controller' => 'Mail',
	));
// 教务处
Route::set('gduf-jwc', 'gduf/jwc.html')
	->defaults(array(
		'directory' => 'Gduf',
		'controller' => 'JWC',
		'action' => 'index',
	));
Route::set('gduf-jwc-action', 'gduf/jwc-<action>.html', array(
		'action' => 'login|fetch',
	))
	->defaults(array(
		'directory' => 'Gduf',
		'controller' => 'JWC',
	));
