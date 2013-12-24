<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_IsAscii extends UTF8_Function {

	const REGEX = '/[^\x00-\x7F]/S';

	/**
	 * Tests whether a string contains only 7-bit ASCII bytes. This is used to
	 * determine when to use native functions or UTF-8 functions.
	 *
	 *     $ascii = UTF8::is_ascii($str);
	 *
	 * @param   mixed   $str    string or array of strings to check
	 * @return  boolean
	 */
	public static function process($str)
	{
		if (is_array($str))
		{
			$str = implode($str);
		}

		return ! preg_match(UTF8_Function_IsASCII::REGEX, $str);
	}

}
