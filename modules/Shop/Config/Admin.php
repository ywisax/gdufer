<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 扩展后台的菜单
 */
return array(

	'navigation' => array(
		__('Shop Manage') => array(
			'admin_controller' => array('Shop'),
			'links' => array(
				__('Item List')		=> Route::url('xunsec-admin', array('controller' => 'Shop', 'action' => 'item')),
				__('Order List')	=> Route::url('xunsec-admin', array('controller' => 'Shop', 'action' => 'order')),
				__('Comment List')	=> Route::url('xunsec-admin', array('controller' => 'Shop', 'action' => 'comment')),
				__('Category List')	=> Route::url('xunsec-admin', array('controller' => 'Shop', 'action' => 'category')),
				__('Cart List')		=> Route::url('xunsec-admin', array('controller' => 'Shop', 'action' => 'cart')),
				__('Shop Currency')	=> Route::url('xunsec-admin', array('controller' => 'Shop', 'action' => 'currency')),
				__('Shop Setting')	=> Route::url('xunsec-admin', array('controller' => 'Shop', 'action' => 'setting')),
			),
		),
	),
);
