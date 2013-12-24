<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 扩展后台的菜单
 */
return array(

	'navigation' => array(
		__('Forum Manage') => array(
			'admin_controller' => array('Forum'),
			'links' => array(
				__('Topic List')	=> Route::url('xunsec-admin', array('controller' => 'Forum', 'action' => 'topic')),
				__('Reply List')	=> Route::url('xunsec-admin', array('controller' => 'Forum', 'action' => 'reply')),
				__('Forum Setting')	=> Route::url('xunsec-admin', array('controller' => 'Forum', 'action' => 'setting')),
				__('Manage Log')	=> Route::url('xunsec-admin', array('controller' => 'Forum', 'action' => 'log')),
			),
		),
	),
);
