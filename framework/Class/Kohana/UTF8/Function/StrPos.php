<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_StrPos extends UTF8_Function {

	/**
	 * Finds position of first occurrence of a UTF-8 string. This is a
	 * UTF8-aware version of [strpos](http://php.net/strpos).
	 *
	 *     $position = UTF8::strpos($str, $search);
	 *
	 * @param   string  $str    haystack
	 * @param   string  $search needle
	 * @param   integer $offset offset from which character in haystack to start searching
	 * @return  integer position of needle
	 * @return  boolean FALSE if the needle is not found
	 */
	public static function process($str, $search, $offset = 0)
	{
		if (UTF8::$server_utf8)
			return mb_strpos($str, $search, $offset, Kohana::$charset);

		$offset = (int) $offset;

		if (UTF8::is_ascii($str) AND UTF8::is_ascii($search))
		{
			return strpos($str, $search, $offset);
		}

		if ($offset == 0)
		{
			$array = explode($search, $str, 2);
			return isset($array[1]) ? UTF8::strlen($array[0]) : FALSE;
		}

		$str = UTF8::substr($str, $offset);
		$pos = UTF8::strpos($str, $search);
		return ($pos === FALSE) ? FALSE : ($pos + $offset);
	}

}
