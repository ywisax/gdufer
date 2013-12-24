<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_StrLen extends UTF8_Function {

	/**
	 * Returns the length of the given string. This is a UTF8-aware version
	 * of [strlen](http://php.net/strlen).
	 *
	 *     $length = UTF8::strlen($str);
	 *
	 * @param   string  $str    string being measured for length
	 * @return  integer
	 */
	public static function process($str)
	{
		if (UTF8::$server_utf8)
			return mb_strlen($str, Kohana::$charset);

		return UTF8::is_ascii($str)
			? strlen($str)
			: strlen(utf8_decode($str));
	}

}
