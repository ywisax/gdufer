<?php defined('SYS_PATH') OR die('No direct access allowed.');

return array
(
	// Enable these packages in the API browser.  TRUE for all packages, or a string of comma seperated packages, using 'None' for a class with no @package
	// Example: 'api_packages' => 'Kohana,Kohana/Database,Kohana/ORM,None',
	'api_packages' => TRUE,
	
	// APP_PATH是否也包括
	'include_apppath' => FALSE,

	// 只允许出现一个'modules'键名
	'modules' => array(
		// This should be the path to this modules userguide pages, without the 'guide/'. Ex: '/guide/modulename/' would be 'modulename'
		'guide' => array(
			// 使用激活且显示在网站上
			'enabled' => TRUE,
			// 显示在网站上的名称
			'name' => '用户手册',
			// 模块的简短介绍
			'description' => '关于本手册的一些说明和使用方法.',
			// 版权信息，会在该模块的页脚显示
			'copyright' => '&copy; 2011-2013 XunSec Team',
		)	
	),

	// Set transparent class name segments
	'transparent_prefixes' => array(
		'Kohana' => TRUE,
	)
);
