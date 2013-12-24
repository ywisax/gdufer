<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_StrCaseCmp extends UTF8_Function {

	/**
	 * Case-insensitive UTF-8 string comparison. This is a UTF8-aware version
	 * of [strcasecmp](http://php.net/strcasecmp).
	 *
	 *     $compare = UTF8::strcasecmp($str1, $str2);
	 *
	 * @param   string  $str1   string to compare
	 * @param   string  $str2   string to compare
	 * @return  integer less than 0 if str1 is less than str2
	 * @return  integer greater than 0 if str1 is greater than str2
	 * @return  integer 0 if they are equal
	 */
	public static function process($str1, $str2)
	{
		if (UTF8::is_ascii($str1) AND UTF8::is_ascii($str2))
		{
			return strcasecmp($str1, $str2);
		}

		$str1 = UTF8::strtolower($str1);
		$str2 = UTF8::strtolower($str2);
		return strcmp($str1, $str2);
	}

}
