<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_StripNonAscii extends UTF8_Function {

	const STRIP_MATCH = '/[^\x00-\x7F]+/S';
	const STRIP_REPLACE = '';

	/**
	 * Strips out all non-7bit ASCII bytes.
	 *
	 *     $str = UTF8::strip_non_ascii($str);
	 *
	 * @param   string  $str    string to clean
	 * @return  string
	 */
	public static function process($str)
	{
		return preg_replace(UTF8_Function_StripNonAscii::STRIP_MATCH, UTF8_Function_StripNonAscii::STRIP_REPLACE, $str);
	}
}
