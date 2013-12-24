<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_Iconv extends UTF8_Function {

	/**
	 * 转换编码
	 */
	public static function process($string, $to = NULL, $from = NULL)
	{
		return UTF8::$server_utf8
			? mb_convert_encoding($string, $to, $from)
			: (function_exists('iconv') ? iconv($from, $to, $string) : FALSE);
	}
}
