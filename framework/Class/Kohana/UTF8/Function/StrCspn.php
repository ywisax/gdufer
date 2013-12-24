<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_StrCspn extends UTF8_Function {

	const PREVENT_MATCH = '#[-[\].:\\\\^/]#';
	const PREVENT_REPLACE = '\\\\$0';

	/**
	 * Finds the length of the initial segment not matching mask. This is a
	 * UTF8-aware version of [strcspn](http://php.net/strcspn).
	 *
	 *     $found = UTF8::strcspn($str, $mask);
	 *
	 * @param   string  $str    input string
	 * @param   string  $mask   mask for search
	 * @param   integer $offset start position of the string to examine
	 * @param   integer $length length of the string to examine
	 * @return  integer length of the initial segment that contains characters not in the mask
	 */
	public static function process($str, $mask, $offset = NULL, $length = NULL)
	{
		if ($str == '' OR $mask == '')
		{
			return 0;
		}

		if (UTF8::is_ascii($str) AND UTF8::is_ascii($mask))
		{
			return ($offset === NULL) ? strcspn($str, $mask) : (($length === NULL) ? strcspn($str, $mask, $offset) : strcspn($str, $mask, $offset, $length));
		}

		if ($offset !== NULL OR $length !== NULL)
		{
			$str = UTF8::substr($str, $offset, $length);
		}

		// 对这些字符进行转义：- [ ] . : \ ^ /
		// The . and : are escaped to prevent possible warnings about POSIX regex elements
		$mask = preg_replace(UTF8_Function_StrCspn::PREVENT_MATCH, UTF8_Function_StrCspn::PREVENT_REPLACE, $mask);
		preg_match('/^[^'.$mask.']+/u', $str, $matches);

		return isset($matches[0]) ? UTF8::strlen($matches[0]) : 0;
	}

}
