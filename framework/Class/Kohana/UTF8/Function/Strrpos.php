<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_Strrpos extends UTF8_Function {

	/**
	 * Finds position of last occurrence of a char in a UTF-8 string. This is
	 * a UTF8-aware version of [strrpos](http://php.net/strrpos).
	 *
	 *     $position = UTF8::strrpos($str, $search);
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
			return mb_strrpos($str, $search, $offset, Kohana::$charset);

		$offset = (int) $offset;

		if (UTF8::is_ascii($str) AND UTF8::is_ascii($search))
		{
			return strrpos($str, $search, $offset);
		}

		if ($offset == 0)
		{
			$array = explode($search, $str, -1);
			return isset($array[0]) ? UTF8::strlen(implode($search, $array)) : FALSE;
		}

		$str = UTF8::substr($str, $offset);
		$pos = UTF8::strrpos($str, $search);
		return ($pos === FALSE) ? FALSE : ($pos + $offset);
	}

}
