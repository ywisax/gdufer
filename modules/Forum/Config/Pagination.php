<?php defined('SYS_PATH') OR die('No direct script access.');

return array(

	// 用于论坛模块的分页配置
	'forum' => array(
		'current_page'      => array('source' => 'route', 'key' => 'page'), // source: "query_string" or "route"
		'total_items'       => 0,
		'items_per_page'    => 10,
		'view'              => 'Pagination.Digg',
		'auto_hide'         => TRUE,
		'first_page_in_url' => TRUE,
	),
);
