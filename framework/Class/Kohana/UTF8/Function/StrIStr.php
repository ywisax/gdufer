<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_StrIStr extends UTF8_Function {

	/**
	 * Case-insenstive UTF-8 version of strstr. Returns all of input string
	 * from the first occurrence of needle to the end. This is a UTF8-aware
	 * version of [stristr](http://php.net/stristr).
	 *
	 *     $found = UTF8::stristr($str, $search);
	 *
	 * @param   string  $str    input string
	 * @param   string  $search needle
	 * @return  string  matched substring if found
	 * @return  FALSE   if the substring was not found
	 */
	public static function process($str, $search)
	{
		if (UTF8::is_ascii($str) AND UTF8::is_ascii($search))
			return stristr($str, $search);

		if ($search == '')
			return $str;

		$str_lower = UTF8::strtolower($str);
		$search_lower = UTF8::strtolower($search);

		preg_match('/^(.*?)'.preg_quote($search_lower, '/').'/s', $str_lower, $matches);

		return isset($matches[1])
			? substr($str, strlen($matches[1]))
			: FALSE;
	}

}
