<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 过滤器配置
 */
return array(

	'gduf_schedule' => array(
		// 允许使用的标签
		'allowed_tags' => array(
			'b' => array(),
			'i' => array(),
			'a' => array(
				'href'  => array('minlen' => 3, 'maxlen' => 50),
				'title' => array('valueless' => 'n')
			),
			'p' => array(
				'align' => 1,
				'style' => 1,
			),
			'img' => array('src' => 1), # FIXME
			'font' => array(
				'size' => array('minval' => 4, 'maxval' => 20),
			),
			'br' => array(),
		),
		
		// 允许使用的协议
		'allowed_protocols' => array('http', 'https'),
	),
);
