<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_UcFirst extends UTF8_Function {

	const REGEX = '/^(.?)(.*)$/us';

	/**
	 * Makes a UTF-8 string's first character uppercase. This is a UTF8-aware
	 * version of [ucfirst](http://php.net/ucfirst).
	 *
	 *     $str = UTF8::ucfirst($str);
	 *
	 * @param   string  $str    mixed case string
	 * @return  string
	 */
	public static function process($str)
	{
		if (UTF8::is_ascii($str))
		{
			return ucfirst($str);
		}

		preg_match(UTF8_Function_UcFirst::REGEX, $str, $matches);
		return UTF8::strtoupper($matches[1]).$matches[2];
	}

}
