<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_RTrim extends UTF8_Function {

	const CHAR_MATCH = '#[-\[\]:\\\\^/]#';
	const CHAR_REPLACE = '\\\\$0';

	/**
	 * Strips whitespace (or other UTF-8 characters) from the end of a string.
	 * This is a UTF8-aware version of [rtrim](http://php.net/rtrim).
	 *
	 *     $str = UTF8::rtrim($str);
	 *
	 * @param   string  $str        input string
	 * @param   string  $charlist   string of characters to remove
	 * @return  string
	 */
	public static function process($str, $charlist = NULL)
	{
		if ($charlist === NULL)
		{
			return rtrim($str);
		}

		if (UTF8::is_ascii($charlist))
		{
			return rtrim($str, $charlist);
		}

		$charlist = preg_replace(UTF8_Function_LTrim::CHAR_MATCH, UTF8_Function_LTrim::CHAR_REPLACE, $charlist);

		return preg_replace('/['.$charlist.']++$/uD', '', $str);
	}

}
