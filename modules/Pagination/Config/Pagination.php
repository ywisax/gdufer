<?php defined('SYS_PATH') OR die('No direct script access.');

return array(

	// Default configuration
	'default' => array(
		'current_page'      => array('source' => 'query_string', 'key' => 'page'), // source: "query_string" or "route"
		'total_items'       => 0,
		'items_per_page'    => 10,
		'view'              => 'Pagination.Basic',
		'auto_hide'         => TRUE,
		'first_page_in_url' => TRUE,
	),

);