<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 一个简单的商店类，店家可以发布商品，会员也可以发布商品。
 */
Kohana::module(array(
	'ORM',
	'Database', // ORM肯定用到数据库啦
	'Currency',
	'Media',
	'XunSec',
));

// 下面添加路由

// 购物车页面
Route::set('shop-cart', 'shop/cart(-<action>).html', array(
		'action' => '',
	))
	->defaults(array(
		'directory' => 'Shop',
		'controller' => 'Cart',
		'action'     => 'index',
	));
