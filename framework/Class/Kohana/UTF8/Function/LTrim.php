<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_LTrim extends UTF8_Function {

	const PROCESS_MATCH = '#[-\[\]:\\\\^/]#';
	const PROCESS_REPLACE = '\\\\$0';

	/**
	 * Strips whitespace (or other UTF-8 characters) from the beginning of
	 * a string. This is a UTF8-aware version of [ltrim](http://php.net/ltrim).
	 *
	 *     $str = UTF8::ltrim($str);
	 *
	 * @param   string  $str        input string
	 * @param   string  $charlist   string of characters to remove
	 * @return  string
	 */
	public static function process($str, $charlist = NULL)
	{
		if ($charlist === NULL)
		{
			return ltrim($str);
		}

		if (UTF8::is_ascii($charlist))
		{
			return ltrim($str, $charlist);
		}

		$charlist = preg_replace(UTF8_Function_LTrim::PROCESS_MATCH, UTF8_Function_LTrim::PROCESS_REPLACE, $charlist);

		return preg_replace('/^['.$charlist.']+/u', '', $str);
	}

}
