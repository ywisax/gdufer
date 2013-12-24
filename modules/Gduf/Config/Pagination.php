<?php defined('SYS_PATH') OR die('No direct script access.');

return array(

	// 个性化的分页设置
	'gduf' => array(
		'current_page'      => array('source' => 'query_string', 'key' => 'page'), // source: "query_string" or "route"
		'total_items'       => 0,
		'items_per_page'    => 20, // 广金默认是20条一页
		'view'              => 'Pagination.Digg',
		'auto_hide'         => TRUE,
		'first_page_in_url' => TRUE,
	),
);
