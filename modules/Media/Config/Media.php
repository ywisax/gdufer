<?php defined('SYS_PATH') or die('No direct script access.');

return array(

	// 开启Cache的话，则会把文件写到缓存去
	'cache'      => FALSE,

	// 默认键名啊
	'cache_prefix' => 'media:~',
	// 缓存作用期
	'cache_lifetime' => 2000,
	
	'use_static' => TRUE,
	// 静态文件替换列表啊，会合并滴
	'static_file' => array(
	),
	
	'cdn_domain' => '',
);
