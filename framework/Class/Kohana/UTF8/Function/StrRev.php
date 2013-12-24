<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_StrRev extends UTF8_Function {

	const REGEX = '/./us';
	const SEPARATOR = '';

	/**
	 * Reverses a UTF-8 string. This is a UTF8-aware version of [strrev](http://php.net/strrev).
	 *
	 *     $str = UTF8::strrev($str);
	 *
	 * @param   string  $str    string to be reversed
	 * @return  string
	 */
	public static function process($str)
	{
		if (UTF8::is_ascii($str))
		{
			return strrev($str);
		}

		preg_match_all(UTF8_Function_StrRev::REGEX, $str, $matches);
		return implode(UTF8_Function_StrRev::SEPARATOR, array_reverse($matches[0]));
	}

}
