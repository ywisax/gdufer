<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_StripAsciiCtrl extends UTF8_Function {

	const ASCII_MATCH = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';
	const ASCII_REPLACE = '';

	/**
	 * Strips out device control codes in the ASCII range.
	 *
	 *     $str = UTF8::strip_ascii_ctrl($str);
	 *
	 * @param   string  $str    string to clean
	 * @return  string
	 */
	public static function process($str)
	{
		return preg_replace(UTF8_Function_StripAsciiCtrl::ASCII_MATCH, UTF8_Function_StripAsciiCtrl::ASCII_REPLACE, $str);
	}
}
