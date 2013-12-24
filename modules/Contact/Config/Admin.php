<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 扩展后台的菜单
 */
return array(

	'navigation' => array(
		__('Contact Manage') => array(
			'admin_controller' => array('Contact'),
			'links' => array(
				__('Contact List')			=> Route::url('xunsec-admin', array('controller' => 'Contact', 'action' => 'list')),
			),
		),
	),

);
