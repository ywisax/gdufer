<?php defined('SYS_PATH') OR die('No direct script access.');

return array(

	'information' => array(
	
		// 用于后台管理的分页配置
		'admin' => array(
			'current_page'      => array('source' => 'route', 'key' => 'id'), // source: "query_string" or "route"
			'total_items'       => 0,
			'items_per_page'    => 20,
			'view'              => 'Pagination.Digg',
			'auto_hide'         => TRUE,
			'first_page_in_url' => TRUE,
		),
	
		// 用于书本模块的分页配置
		'book' => array(
			'current_page'      => array('source' => 'route', 'key' => 'id'), // source: "query_string" or "route"
			'total_items'       => 0,
			'items_per_page'    => 10,
			'view'              => 'Pagination.Digg',
			'auto_hide'         => TRUE,
			'first_page_in_url' => TRUE,
		),
	),
);
