<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 扩展后台的菜单
 */
return array(

	'navigation' => array(
		__('CMS Manage') => array(
			'admin_controller' => array('Page', 'Snippet', 'Layout', 'Redirect'),
			'links' => array(
				__('Page Manage')		=> Route::url('xunsec-admin', array('controller' => 'Page')),
				__('Snippet Manage')	=> Route::url('xunsec-admin', array('controller' => 'Snippet')),
				__('Layout Manage')	=> Route::url('xunsec-admin', array('controller' => 'Layout')),
				__('Redirect Manage')	=> Route::url('xunsec-admin', array('controller' => 'Redirect')),
				__('CMS Logs')		=> Route::url('xunsec-admin', array('controller' => 'Log')),
			),
		),
	),
);
