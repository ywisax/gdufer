<?php defined('SYS_PATH') OR die('No direct script access.');

Kohana::module(array(
	'ORM',
	'Database', // ORM肯定用到数据库啦
	'Media',
	'Pagination', // 用到分页
	'Auth',
	'Attachment',
	'Captcha',
));

// 下面添加路由

// 论坛首页
Route::set('forum-list', 'forum(/<page>).html', array(
		'page' => '\d+',
	))
	->defaults(array(
		'directory' => 'Forum',
		'controller' => 'Group',
		'action' => 'index',
	));
// 论坛分类列表
Route::set('forum-group', 'forum/group-<group>(/<page>).html', array(
		'group' => '\d+',
		'page' => '\d+',
	))
	->defaults(array(
		'directory' => 'Forum',
		'controller' => 'Group',
		'action' => 'index',
	));
// 群组操作
Route::set('forum-group-action', 'forum/group-<action>(-<id>).html', array(
		'action' => 'new|edit|delete',
	))
	->defaults(array(
		'directory' => 'Forum',
		'controller' => 'Group',
	));
// 查看帖子
Route::set('forum-topic', 'forum/topic-<id>(-<page>).html', array(
		'id' => '\d+',
		'page' => '\d+',
	))
	->defaults(array(
		'directory' => 'Forum',
		'controller' => 'Topic',
		'action' => 'view',
	));
// 帖子操作
Route::set('forum-topic-action', 'forum/topic-<action>(-<id>).html', array(
		'action' => 'new|edit|delete|sticky|visible|close',
		'id' => '\d+',
	))
	->defaults(array(
		'directory' => 'Forum',
		'controller' => 'Topic',
	));
// 回复处理
Route::set('forum-reply-action', 'forum/reply-<action>(-<id>).html', array(
		'action' => 'new|edit|delete',
		'id' => '\d+',
	))
	->defaults(array(
		'directory' => 'Forum',
		'controller' => 'Reply',
	));
// 论坛侧边栏
Route::set('forum-sidebar', 'forum/sidebar.html')
	->defaults(array(
		'controller' => 'Forum',
		'action' => 'sidebar',
	));
// 论坛搜索
Route::set('forum-search', 'forum/search(-<keyword>).html')
	->defaults(array(
		'directory' => 'Forum',
		'controller' => 'Search',
		'action' => 'index',
	));
