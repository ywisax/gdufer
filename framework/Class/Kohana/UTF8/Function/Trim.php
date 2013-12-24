<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_Trim extends UTF8_Function {

	/**
	 * Strips whitespace (or other UTF-8 characters) from the beginning and
	 * end of a string. This is a UTF8-aware version of [trim](http://php.net/trim).
	 *
	 *     $str = UTF8::trim($str);
	 *
	 * @param   string  $str        input string
	 * @param   string  $charlist   string of characters to remove
	 * @return  string
	 */
	public static function process($str, $charlist = NULL)
	{
		return ($charlist === NULL)
			? trim($str)
			: UTF8::ltrim(UTF8::rtrim($str, $charlist), $charlist);
	}

}
