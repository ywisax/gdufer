<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 扩展后台的菜单
 */
return array(

	'navigation' => array(
		__('User Manage') => array(
			'admin_controller' => array('Auth'),
			'links' => array(
				__('User List')		=> Route::url('xunsec-admin', array('controller' => 'Auth')),
				__('New User')		=> Route::url('xunsec-admin', array('controller' => 'Auth', 'action' => 'new')),
			),
		),
	),

);
