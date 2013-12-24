<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 扩展后台的菜单
 */
return array(

	'navigation' => array(
		__('Attachment Manage') => array(
			'admin_controller' => array('Attachment'),
			'links' => array(
				__('Attachment List')			=> Route::url('xunsec-admin', array('controller' => 'Attachment', 'action' => 'list')),
				__('Attachment Setting')		=> Route::url('xunsec-admin', array('controller' => 'Attachment', 'action' => 'setting')),
			),
		),
	),

);
