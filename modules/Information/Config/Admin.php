<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 扩展后台的菜单
 */
return array(

	'navigation' => array(
		__('Information Manage') => array(
			'admin_controller' => array('Information'),
			'links' => array(
				__('Information List')			=> Route::url('xunsec-admin', array('controller' => 'Information', 'action' => 'list')),
				__('Information Setting')	=> Route::url('xunsec-admin', array('controller' => 'Information', 'action' => 'setting')),
			),
		),
	),
);
