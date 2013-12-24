<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 扩展后台的菜单
 */
return array(

	'navigation' => array(
		__('Weibo Manage') => array(
			'admin_controller' => array('Weibo'),
			'links' => array(
				__('Weibo List')			=> Route::url('xunsec-admin', array('controller' => 'Weibo')),
				__('Authentication User')	=> Route::url('xunsec-admin', array('controller' => 'Weibo', 'action' => 'user')),
				__('Weibo Setting')			=> Route::url('xunsec-admin', array('controller' => 'Weibo', 'action' => 'setting')),
				__('Manage Log')			=> Route::url('xunsec-admin', array('controller' => 'Weibo', 'action' => 'log')),
			),
		),
	),

);
