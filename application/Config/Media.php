<?php defined('SYS_PATH') OR die('No direct script access.');

return array(
	
	// CDN文件替代表，省点流量啊
	'static_file' => array(
	
		// bootstrap的css和js文件
		'bootstrap/css/bootstrap.css' => 'http://libs.baidu.com/bootstrap/2.3.1/css/bootstrap.min.css',
		'bootstrap/css/bootstrap.min.css' => 'http://libs.baidu.com/bootstrap/2.3.1/css/bootstrap.min.css',
		'bootstrap/css/bootstrap-responsive.css' => 'http://lib.sinaapp.com/js/bootstrap/2.3.1/css/bootstrap-responsive.min.css',
		'bootstrap/css/bootstrap-responsive.min.css' => 'http://lib.sinaapp.com/js/bootstrap/2.3.1/css/bootstrap-responsive.min.css',
		'bootstrap/js/bootstrap.js' => 'http://libs.baidu.com/bootstrap/2.3.1/js/bootstrap.min.js',
		'bootstrap/js/bootstrap.min.js' => 'http://libs.baidu.com/bootstrap/2.3.1/js/bootstrap.min.js',

		// jq的插件
		'jquery/jquery.cookie.js' => 'http://game.qq.com/portal2010/js/cookie.js',
		'jquery/jquery.cookie.min.js' => 'http://game.qq.com/portal2010/js/cookie.js',
		//'jquery/jquery.treeview.js' => 'http://jquery.bassistance.de/treeview/jquery.treeview.js', // 这个速度不怎么快，勉强能用吧
		//'jquery/jquery.treeview.min.js' => 'http://jquery.bassistance.de/treeview/jquery.treeview.js', // 这个速度不怎么快，勉强能用吧

		// jq
		'jquery/jquery-1.7.2.min.js' => 'http://www.hao123.com/js/common/lib/1.7.2.jquery.min.js',
		
		// nivo-slider，国外的CDN？
		//'nivo-slider/jquery.nivo.slider.pack.js' => 'http://cdn.jsdelivr.net/nivoslider/3.2/jquery.nivo.slider.js',
		//'nivo-slider/nivo-slider.css' => 'http://cdn.jsdelivr.net/nivoslider/3.2/nivo-slider.css',
		//'nivo-slider/themes/default/default.css' => 'http://cdn.jsdelivr.net/nivoslider/3.2/themes/default/default.css',
		
		'html5shiv.js' => 'http://source1.qq.com/wsd/html5.js',
	),
	
	//'cdn_domain' => 'http://gdufcdn.sinaapp.com',
	'cdn_domain' => 'http://gdufer.qiniudn.com',
);
