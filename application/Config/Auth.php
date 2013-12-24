<?php defined('SYS_PATH') OR die('No direct access allowed.');

return array(
	// 不用随便更改这个配置啊啊啊啊
	'driver'       => 'ORM',
	'hash_method'  => 'sha256',
	'hash_key'     => 'NULL',
	'lifetime'     => 1209600,
	'session_type' => Session::$default,
	'session_key'  => 'auth_user',
);
